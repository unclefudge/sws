@extends('layout')

@section('breadcrumbs')
    <ul class="page-breadcrumb breadcrumb">
        <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
        @if (Auth::user()->company->subscription > 1 && Auth::user()->hasAnyPermissionType('company'))
            <li><a href="/company">Companies</a><i class="fa fa-circle"></i></li>
        @endif
        <li><a href="/company/{{ $company->id }}/doc">Documents</a><i class="fa fa-circle"></i></li>
        <li><span>Period Trade Contract</span></li>
    </ul>
@stop

@section('content')
    <div class="page-content-inner">

        @include('company/_header')

        <div class="row">
            <div class="col-md-12">
                <div class="note note-warning">
                    This form have been created using details from your <a href="/company/{{ $company->id }}" target="_blank">Company Profile</a> as well as documents uploaded such as:
                    <ul>
                        <li>{!! ($company->activeCompanyDoc('7') && $company->activeCompanyDoc('7')->status == 1) ? "<a href='".$company->activeCompanyDoc('7')->attachment_url."' target='_blank'>Contractors Licence</a>" : 'Contractors Licence' !!}</li>
                        <li>{!! ($company->activeCompanyDoc('1') && $company->activeCompanyDoc('1')->status == 1) ? "<a href='".$company->activeCompanyDoc('1')->attachment_url."' target='_blank'>Public Liability</a>" : 'Public Liability' !!}</li>
                        <li>{!! ($company->activeCompanyDoc('2') && $company->activeCompanyDoc('2')->status == 1) ? "<a href='".$company->activeCompanyDoc('2')->attachment_url."' target='_blank'>Workers Compensation</a>" : 'Workers Compensation' !!}</li>
                        <li>{!! ($company->activeCompanyDoc('3') && $company->activeCompanyDoc('3')->status == 1) ? "<a href='".$company->activeCompanyDoc('3')->attachment_url."' target='_blank'>Sickness & Accident</a>" : 'Sickness & Accident' !!}</li>
                    </ul>
                    If any information within this contract are incorrect please update your above details / documents or contact <a href="mailto:{!! ($company->reportsTo()->id == 3) ? 'accounts1@capecode.com.au' : $company->reportsTo()->email !!}">{{ $company->reportsTo()->name }}</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-dark bold uppercase"> Period Trade Contract </span>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        {!! Form::model('CompanyDocPeriodTrade', ['action' => ['Company\CompanyPeriodTradeController@store', $company->id], 'class' => 'horizontal-form']) !!}
                        @include('form-error')

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 style="margin: -20px 0 0 0">SCHEDULE</h5><br>
                                </div>
                            </div>

                            {{-- Schedule 1. Date --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">1.</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        <h4 style="margin: 0px">Date</h4>
                                        <hr style="margin: 5px 0px 5px 0px">
                                        AN AGREEMENT DATED &nbsp; <span class="font-grey-silver">(time of signing)</span>
                                    </td>
                                </tr>
                            </table>

                            {{-- Schedule 2. Principle Contractor --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">2.</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        <h4 style="margin: 0px">Principal contractor</h4>
                                        <hr style="margin: 5px 0px 0px 0px">
                                    </td>
                                </tr>
                                <tr>
                                    <td width="5%" class="pad0" style="border: 0px"><h5 style="margin: 0px">&nbsp;</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">NAME </span>
                                            <span style="display: table-cell;">{{ $company->reportsTo()->name }}</span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">ADDRESS </span>
                                            <span style="display: table-cell;">{!! $company->reportsTo()->address_formatted !!}<br></span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">ABN </span>
                                            <span style="display: table-cell; width: 200px;">{{ $company->reportsTo()->abn }}</span>
                                            <span style="display: table-cell;">ACN </span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">PHONE </span>
                                            <span style="display: table-cell">{{ $company->reportsTo()->phone }}</span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">EMAIL </span>
                                            <span style="display: table-cell">{!! ($company->reportsTo()->id == 3) ? 'accounts1@capecode.com.au' : $company->reportsTo()->email !!}</span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 190px;">HIA MEMBER NUMBER </span>
                                            <span style="display: table-cell">&nbsp;</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>


                            {{-- Schedule 3. Tade Contractor --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">3.</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        <h4 style="margin: 0px">Trade contractor</h4>
                                        <hr style="margin: 5px 0px 0px 0px">
                                    </td>
                                </tr>
                                <tr>
                                    <td width="5%" class="pad0" style="border: 0px"><h5 style="margin: 0px">&nbsp;</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">NAME </span>
                                            <span style="display: table-cell">{{ $company->name }}</span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">ADDRESS </span>
                                            <span style="display: table-cell">{!! $company->address_formatted !!}<br></span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">ABN </span>
                                            <span style="display: table-cell; width: 200px;">{{ $company->abn }}</span>
                                            <span style="display: table-cell;">ACN </span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">PHONE </span>
                                            <span style="display: table-cell">{{ $company->phone }}</span>
                                        </div>
                                        <div style="width: 100%; display: table;">
                                            <span style="display: table-cell; width: 90px;">EMAIL </span>
                                            <span style="display: table-cell">{{ $company->email }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            {{-- Schedule 4 --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0px 0px 0px; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">4.</h5></td>
                                    <td class="pad0" style="border: 0px;">
                                        In consideration of:
                                        <ol type="a" style="padding-left:25px; margin-bottom: 5px">
                                            <li>the <b>trade contractor</b> agreeing to quote for <b>trade works</b> whenever asked by the <b>principal contractor</b>, and</li>
                                            <li>the <b>principal contractor</b> agreeing to pay, on demand by the <b>trade contractor</b>, the sum of $1,</li>
                                        </ol>
                                        the parties agree that the period trade contract conditions overleaf are deemed to be incorporated into each <b>trade contract</b> for a period of 12 months from the date of this agreement.
                                    </td>
                                </tr>
                            </table>

                            {{-- Schedule 5 --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">5.</h5></td>
                                    <td class="pad0" style="border: 0px">
                                        The <b>trade contractor</b> acknowledges and agrees that:
                                        <ol type="a" style="padding-left:25px; margin-bottom: 5px">
                                            <li>the <b>principal contractor</b> has not made any representation, and</li>
                                            <li>the <b>trade contractor</b> has not relied on any representation made by the <b>principal contractor</b>,</li>
                                        </ol>
                                        as to the availability of work or the number of work orders that will be issued by the <b>principal contractor</b>.
                                    </td>
                                </tr>
                            </table>

                            {{-- Schedule 6 --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">6.</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        The parties agree that for each <b>trade contract</b> the scope of the <b>trade works</b>, the commencement and completion dates of the <b>trade works</b> and the price of <b>trade
                                            works</b> will be set out:
                                        <ol type="a" style="padding-left:25px">
                                            <li>in a quote from the <b>trade contractor</b> that is accepted by the <b>principal contractor</b>;</li>
                                            <li>in a work order issued by the <b>principal contractor</b> that is accepted by the <b>trade contractor</b>; or</li>
                                            <li>as otherwise evidenced in writing and signed by the parties.</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            {{-- Schedule 7 --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">7.</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        The parties agree that this agreement does not form a contract to carry out work. The obligation to carry out work arises on the formation
                                        of a <b>trade contract</b>
                                        as described in paragraph 6 above.
                                    </td>
                                </tr>
                            </table>

                            {{-- Schedule 8 --}}
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">8.</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        <b>"Defects liability period"</b> in a <b>trade contract</b> means a period of 12 weeks from the practical completion of the work under the
                                        <b>head contract</b>.
                                    </td>
                                </tr>
                            </table>

                            {{-- Schedule 9 --}}
                            <hr style="margin: 5px 0px 5px 0px">
                            <table class="table" style="padding: 0px; margin: 0px">
                                <tr>
                                    <td width="5%" style="margin:5px 0 0 0; padding: 5px 0px; border: 0px"><h5 style="margin: 0px">9.</h5></td>
                                    <td class="pad5" style="border: 0px">
                                        <h5 style="margin: 0px">INFORMATION TO BE COMPLETED BY THE TRADE CONTRACTOR</h5>
                                    </td>
                                </tr>
                            </table>

                            {{-- Licence No. --}}
                            <br><br>
                            <div style="width: 100%; display: table;">
                                <span style="display: table-cell; width: 200px;">LICENCE NO (if required) </span>
                                <span style="display: table-cell; border-bottom: 1px solid #eee; border-top: 0px">{!! ($company->activeCompanyDoc('7') && $company->activeCompanyDoc('7')->status == 1) ? $company->activeCompanyDoc('7')->ref_no : '' !!}</span>
                            </div>
                            <br>

                            {{-- Public Liabilty --}}
                            <h4 style="margin-bottom: 3px">PUBLIC LIABILITY INSURANCE</h4>
                            <div style="width: 100%; display: table;">
                                <span style="display: table-cell; width: 30px;">&nbsp;</span>
                                <span style="display: table-cell; width: 120px;">COMPANY</span>
                                <span style="display: table-cell; border-bottom: 1px solid #eee; border-top: 0px">{!! ($company->activeCompanyDoc('1') && $company->activeCompanyDoc('1')->status == 1) ? $company->activeCompanyDoc('1')->ref_name : '' !!}</span>
                            </div>
                            <br>
                            <div style="width: 100%; display: table;">
                                <span style="display: table-cell; width: 30px;">&nbsp;</span>
                                <span style="display: table-cell; width: 120px;">POLICY NO</span>
                                <span style="display: table-cell; width: 340px; border-bottom: 1px solid #eee; border-top: 0px">{!! ($company->activeCompanyDoc('1') && $company->activeCompanyDoc('1')->status == 1) ? $company->activeCompanyDoc('1')->ref_no : '' !!}</span>
                                <span style="display: table-cell; width: 50px;">&nbsp;</span>
                                <span style="display: table-cell; width: 120px;">CURRENT TO</span>
                                <span style="display: table-cell; border-bottom: 1px solid #eee; border-top: 0px">{!! ($company->activeCompanyDoc('1') && $company->activeCompanyDoc('1')->status == 1) ? $company->activeCompanyDoc('1')->expiry->format('d/m/Y') : '' !!}</span>
                            </div>
                            <br>

                            {{-- Workers Comp --}}
                            <h4 style="margin-bottom: 3px">WORKERS COMPENSATION INSURANCE</h4>
                            <div style="width: 100%; display: table;">
                                <span style="display: table-cell; width: 30px;">&nbsp;</span>
                                <span style="display: table-cell; width: 120px;">COMPANY</span>
                                <span style="display: table-cell; border-bottom: 1px solid #eee; border-top: 0px">{!! ($company->activeCompanyDoc('2') && $company->activeCompanyDoc('2')->status == 1) ? $company->activeCompanyDoc('2')->ref_name : '' !!}</span>
                            </div>
                            <br>
                            <div style="width: 100%; display: table;">
                                <span style="display: table-cell; width: 30px;">&nbsp;</span>
                                <span style="display: table-cell; width: 120px;">POLICY NO</span>
                                <span style="display: table-cell; width: 340px; border-bottom: 1px solid #eee; border-top: 0px">{!! ($company->activeCompanyDoc('2') && $company->activeCompanyDoc('2')->status == 1) ? $company->activeCompanyDoc('2')->ref_no : '' !!}</span>
                                <span style="display: table-cell; width: 50px;">&nbsp;</span>
                                <span style="display: table-cell; width: 120px;">CURRENT TO</span>
                                <span style="display: table-cell; border-bottom: 1px solid #eee; border-top: 0px">{!! ($company->activeCompanyDoc('2') && $company->activeCompanyDoc('2')->status == 1) ? $company->activeCompanyDoc('2')->expiry->format('d/m/Y') : '' !!}</span>
                            </div>
                            <br>

                            {{-- Sickness + Accident --}}
                            <h4 style="margin-bottom: 3px">SICKNESS & ACCIDENT INSURANCE</h4>
                            <div style="width: 100%; display: table;">
                                <span style="display: table-cell; width: 30px;">&nbsp;</span>
                                <span style="display: table-cell; width: 120px;">COMPANY</span>
                                <span style="display: table-cell; border-bottom: 1px solid #eee; border-top: 0px">{!! ($company->activeCompanyDoc('3') && $company->activeCompanyDoc('3')->status == 1) ? $company->activeCompanyDoc('3')->ref_name : '' !!}</span>
                            </div>
                            <br>
                            <div style="width: 100%; display: table;">
                                <span style="display: table-cell; width: 30px;">&nbsp;</span>
                                <span style="display: table-cell; width: 120px;">POLICY NO</span>
                                <span style="display: table-cell; width: 340px; border-bottom: 1px solid #eee; border-top: 0px">{!! ($company->activeCompanyDoc('3') && $company->activeCompanyDoc('3')->status == 1) ? $company->activeCompanyDoc('3')->ref_no : '' !!}</span>
                                <span style="display: table-cell; width: 50px;">&nbsp;</span>
                                <span style="display: table-cell; width: 120px;">CURRENT TO</span>
                                <span style="display: table-cell; border-bottom: 1px solid #eee; border-top: 0px">{!! ($company->activeCompanyDoc('3') && $company->activeCompanyDoc('3')->status == 1) ? $company->activeCompanyDoc('3')->expiry->format('d/m/Y') : '' !!}</span>
                            </div>
                            <br>

                            {{-- ABN + GST --}}
                            <br><br>
                            <div style="width: 100%; display: table;">
                                <span style="display: table-cell; width: 60px;">ABN</span>
                                <span style="display: table-cell; width: 150px; border-bottom: 1px solid #eee; border-top: 0px">{{ $company->abn }}</span>
                                <span style="display: table-cell; width: 50px;">&nbsp;</span>
                                <span style="display: table-cell; width: 250px;">ARE YOU REGISTERED FOR GST?</span>
                                <span style="display: table-cell; width: 150px; border-bottom: 1px solid #eee; border-top: 0px">@if($company->gst) Yes @elseif($company->gst == '0') No @else "&nbsp;" @endif</span>
                                <span style="display: table-cell;">&nbsp;</span>
                            </div>

                            {{-- Signature --}}
                            <br><br><br>
                            THE PARTIES AGREE that the period trade contract conditions referred to above are those that appear here (<a href="/filebank/period_trade_contract_conditions.pdf" target="_blank">see conditions</a>)<br><br><br><br><br>

                            <div class="row">
                                <div class="form-group">
                                    {!! Form::label('contractor_signed_name', "Trade Contractor's Signature", ['class' => 'col-md-3 control-label']) !!}
                                    <div class="col-md-6">
                                        {!! Form::textarea('contractor_signed_name', null, ['rows' => '3', 'class' => 'form-control', 'readonly']) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <a data-original-title="Assign Users" data-toggle="modal" href="#modal_sign_contractor">
                                        <button type="button" class="btn green" id="sign_contractor"> Sign Contract</button>
                                    </a>
                                </div>
                            </div>

                            <br><br>
                            <div><b>Once you have signed this contact please click the "Submit" button to complete the submission.</b></div>

                            <br><br>
                            <div class="form-actions right">
                                <a href="/company/{{ $company->id }}/doc" class="btn default"> Back</a>
                                <button type="submit" name="save" value="save" class="btn green" id="submit" style="display: none;">Submit</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Sign Contractors Modal -->
    <div id="modal_sign_contractor" class="modal fade bs-modal-sm" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form action="#" method="POST" name="form_sign_contractor" id="form_sign_contractor">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title text-center"><b>Sign Contract</b></h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">By signing this contract you accept the name entered below as your digital signature.<br></p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('signed_name', 'Enter your Full Name', ['class' => 'control-label']) !!}
                                    {!! Form::text('signed_name', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn green" data-dismiss="modal" id="sign_contractor_accept">Sign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('page-level-plugins-head')
@stop

@section('page-level-plugins')
    <script src="/js/moment.min.js" type="text/javascript"></script>
@stop

@section('page-level-scripts') {{-- Metronic + custom Page Scripts --}}
<script>
    $(document).ready(function () {
        $('#sign_contractor_accept').on('click', function () {
            var name = $('#signed_name').val();
            var user = "{!! Auth::user()->fullname !!}";
            var email = "{!! (Auth::user()->email) ?  ' ('.Auth::user()->email.')' : '' !!}";
            var date = moment().format('DD/MM/YYYY, h:mm:ss a');
            var signed_string = name + "\n" + 'Digitally signed by ' + user + email + "\nDate: " + date;
            $('#contractor_signed_name').val(signed_string);
            if (name != '')
                $('#submit').show();
            else {
                $('#contractor_signed_name').val('');
                $('#submit').hide();
            }
        });
    });

</script>
@stop