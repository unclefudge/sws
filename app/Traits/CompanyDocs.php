<?php
namespace App\Traits;

use DB;
use Auth;
use Session;
use App\User;
use App\Models\Company\CompanyDoc;
use App\Models\Company\CompanyDocCategory;
use App\Models\Misc\ContractorLicence;
use App\Models\Misc\ComplianceOverride;
use App\Http\Utilities\UserDocTypes;
use App\Http\Utilities\CompanyDocTypes;
use Carbon\Carbon;


trait CompanyDocs {

    /**
     * A Company may have many CompanyDocs
     *
     * @return Collection
     */
    public function companyDocs($category_id = '', $status = '')
    {
        if ($category_id)
            return ($status == '') ? CompanyDoc::where('category_id', $category_id)->where('for_company_id', $this->id)->get() :
                CompanyDoc::where('status', $status)->where('category_id', $category_id)->where('for_company_id', $this->id)->get();
        else
            return ($status == '') ? CompanyDoc::where('for_company_id', $this->id)->get() : CompanyDoc::where('status', $status)->where('for_company_id', $this->id)->get();
    }

    /**
     * First active CompanyDoc of a specific type
     *
     * @return CompanyDoc record
     */
    public function activeCompanyDoc($category_id)
    {
        return CompanyDoc::where('category_id', $category_id)->where('for_company_id', $this->id)->where('status', '>', '0')->first();
    }

    /**
     * Expired CompanyDoc of a specific type
     *
     * @return CompanyDoc record
     */
    public function expiredCompanyDoc($category_id)
    {
        $doc = CompanyDoc::where('category_id', $category_id)->where('for_company_id', $this->id)->where('status', '>', '0')->first();
        if ($doc)
            return ($doc->expiry->lt(Carbon::today())) ? 'Expired ' . $doc->expiry->format('d/m/Y') : null;
        else
            return 'N/A';
    }

    /**
     * Dropdown Option for Contractor Licence
     *
     * @return string
     */
    public function contractorLicenceOptions()
    {
        $doc = CompanyDoc::where('category_id', 7)->where('for_company_id', $this->id)->where('status', '>', '0')->first();
        if ($doc)
            return ContractorLicence::find(1)->classOptions(explode(',', $doc->ref_type));

        return ContractorLicence::find(1)->classOptions();
    }

    /**
     * Contractor Licence Class SBC
     *
     * @return string
     */
    public function contractorLicenceSBC()
    {
        $str = '';
        $doc = CompanyDoc::where('category_id', 7)->where('for_company_id', $this->id)->where('status', '>', '0')->first();
        if ($doc) {
            foreach (explode(',', $doc->ref_type) as $class_id) {
                $lic = ContractorLicence::find($class_id);
                if ($lic)
                    $str .= $lic->name . ', ';
            }
        }

        return rtrim($str, ', ');
    }

    /**
     * Determine if a certain document type is Required
     *
     * @return boolean
     */
    public function requiresCompanyDoc($type, $system = false)
    {
        // Doc types
        // 1  PL - Public Liabilty
        // 2  WC - Workers Comp
        // 3  SA - Sickness & Accident
        // 4  Sub - Subcontractors Statement
        // 5  PTC - Period Trade Contract
        // 6  TT - Test & Tag
        // 7  CL - Contractors Licence
        //
        // Categories                          | PL  |WC/SA| SUB | PTC | CL  |
        // 0  Unallocated                      |_____|_____|_____|_____|_____|
        // 1  Subcontractor (On Site Trade)    |__X__|__X__|__X__|__X__|__X__|
        // 2  Service Provider (On Site trade  |__X__|__X__|_____|_____|__X__|
        // 3  Service Provider (Off Site)      |_____|__X__|_____|_____|_____|
        // 4  Supply & Fit                     |__X__|__X__|_____|_____|__X__|
        // 5  Supply Only                      |__X__|_____|_____|_____|_____|
        // 6  Consultant                       |__X__|__X__|__X__|_____|_____|
        // 7  Builder                          |__X__|__X__|_____|_____|_____|

        // If System == False then check for any Compliance Overrides
        if (!$system) {
            $override = ComplianceOverride::where('type', "cd$type")->where('for_company_id', $this->id)->where('status', 1)->first();
            if ($override)
                return ($override->required) ? true : false;
        }

        // Determine WC or SA
        if (in_array($this->category, [1, 2, 3, 4, 6, 7])) {  // All but 'Supply Only'
            if ($type == 2 && in_array($this->business_entity, ['1', 'Company', '4', 'Trading Trust'])) return true;
            if ($type == 3 && in_array($this->business_entity, ['2', 'Partnership', '3', 'Sole Trader'])) return true;
        }

        // Subcontractor (On Site Trade)
        if ($this->category == 1 && (in_array($type, [1, 4]) || ($this->parent_company == 3 && $type == 5))) return true; // Requires PL, Sub, + PTC (CC Only)
        if ($this->category == 1 && $type == 7 && $this->tradeRequiresContractorsLicence()) return true;

        // Service Provider (On Site Trades) or Supply & Fit
        if (in_array($this->category, [2, 4]) && $type == 1) return true; // Requires PL
        if (in_array($this->category, [2, 4]) && $type == 7 && $this->tradeRequiresContractorsLicence()) return true;

        // Supply Only
        if ($this->category == 5 && $type == 1) return true; // Requires PL

        // Consultant
        if ($this->category == 6 && in_array($type, [1, 4])) return true; // Requires PL + PTC

        // Builder
        if ($this->category == 7 && in_array($type, [1, 7])) return true; // Requires PL + BL

        return false;
    }

    /**
     * Determine if a certain document type is Required
     *
     * @return boolean
     */
    public function requiresCompanyDocText($type)
    {
        return ($this->requiresCompanyDoc($type) ? '<span class="font-red">Required</span>' : '');
    }

    /**
     * Determine if Contractors Licence is Required for any of their trades
     *
     * @return boolean
     */
    public function tradeRequiresContractorsLicence()
    {
        if ($this->category == '1' || $this->category == '2' || $this->category == '4') {
            foreach ($this->tradesSkilledIn as $trade) {
                if ($trade->licence_req)
                    return 1;
            }
        }

        return 0;
    }

    /**
     * Determine if Company is compliant ie. has required docs.
     *
     * @return booleen
     */
    public function isCompliant()
    {
        $doc_types = [1, 2, 3, 4, 5, 7];
        foreach ($doc_types as $type) {
            if ($this->requiresCompanyDoc($type) && (!$this->activeCompanyDoc($type) || $this->activeCompanyDoc($type)->status != 1))
                return false;
        }
        return true;
    }

    /**
     * Documents required for a company to be compliant
     *
     * @return Text or Array
     */
    public function compliantDocs($format = 'array')
    {
        $doc_types = [1 => 'Public Liability', 2 => "Worker's Compensation", 3 => 'Sickness & Accident Insurance', 4 => 'Subcontractors Statement', 5 => 'Period Trade Contract', 7 => 'Contractor Licence'];
        $compliant_docs = [];
        $compliant_html = '';

        foreach ($doc_types as $type => $name) {
            if ($this->requiresCompanyDoc($type)) {
                $compliant_docs[$type] = $name;
                $compliant_html .= "$name, ";
            }
        }

        $compliant_html = rtrim($compliant_html, ', ');

        return ($format == 'csv') ? $compliant_html : $compliant_docs;

    }

    /**
     * Documents company has but aren't required to be compliant
     *
     * @return Text or Array
     */
    public function nonCompliantDocs($format = 'array', $status = '')
    {
        $compliant_docs = $this->compliantDocs();
        $non_compliant_docs = [];
        $non_compliant_html = '';

        foreach ($this->companyDocs() as $doc) {
            if ($doc->status && !isset($compliant_docs[$doc->category_id])) {
                if ($status != '' && $doc->status != $status)
                    continue;
                $non_compliant_docs[$doc->category_id] = $doc->name;
                $non_compliant_html .= "$doc->name, ";
            }
        }

        $non_compliant_html = rtrim($non_compliant_html, ', ');

        return ($format == 'csv') ? $non_compliant_html : $non_compliant_docs;
    }

    /**
     * List of Compliance overrides set for this company
     */
    public function complianceOverrides()
    {
        return ComplianceOverride::where('for_company_id', $this->id)->where('status', 1)->get();
    }

    /**
     * List of Compliance overrides set for this company
     */
    public function parentUpload()
    {
        return (ComplianceOverride::where('type', 'cdu')->where('for_company_id', $this->id)->where('status', 1)->first()) ? true : false;
    }


    /**
     * Missing Company Documents to be compliant
     *
     * @return Text or Array
     */
    public function missingDocs($format = 'array')
    {
        $doc_types = [1 => 'Public Liability', 2 => "Worker's Compensation", 3 => 'Sickness & Accident Insurance', 4 => 'Subcontractors Statement', 5 => 'Period Trade Contract', 7 => 'Contractor Licence'];
        $missing_docs = [];
        $missing_html = '';

        foreach ($doc_types as $type => $name) {
            if ($this->requiresCompanyDoc($type) && (!$this->activeCompanyDoc($type) || $this->activeCompanyDoc($type)->status != 1)) {
                $missing_docs[$type] = $name;
                if ($this->activeCompanyDoc($type)) {
                    $missing_status = ($this->activeCompanyDoc($type)->status == 2) ? 'label-warning' : 'label-danger';
                    $missing_html .= "<span class='label label-sm $missing_status'>$name</span>, ";
                } else
                    $missing_html .= "$name, ";
            }

        }

        $missing_html = rtrim($missing_html, ', ');

        return ($format == 'csv') ? $missing_html : $missing_docs;

    }
}