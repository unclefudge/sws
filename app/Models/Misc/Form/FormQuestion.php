<?php

namespace App\Models\Misc\Form;

use URL;
use Mail;
use App\User;
use App\Models\Misc\Form\FormTemplate;
use App\Models\Misc\Form\FormPage;
use App\Models\Misc\Form\FormSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FormQuestion extends Model {

    protected $table = 'form_questions';
    protected $fillable = ['section_id', 'name', 'type', 'type_special', 'type_version', 'order', 'default', 'multiple', 'required',
        'placeholder', 'helper', 'width', 'notes', 'status', 'created_by', 'created_at', 'updated_at', 'updated_by'];


    /*
     * A FormQuestion belongs to a FormSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        return $this->belongsTo('App\Models\Misc\Form\FormSection', 'section_id')->get();
    }


    /**
     * A FormQuestion 'may' have many options
     *
     */
    public function options()
    {
        if ($this->type == 'select') {
            if ($this->type_special) {
                $option_ids = [];
                if ($this->type_special == 'CONN') $option_ids = [1, 2, 3, 4];
                if ($this->type_special == 'YN') $option_ids = [5, 6];
                if ($this->type_special == 'YrN') $option_ids = [7, 8];
                if ($this->type_special == 'YgN') $option_ids = [9, 10];
                if ($this->type_special == 'YNNA') $option_ids = [11, 12, 13];

                return FormOption::find($option_ids)->sortBy('order');
            } else
                return FormOption::where('question_id', $this->id)->where('status')->sortBy('order');
        }

        return null;
    }

    /**
     * A FormQuestion 'may' have many options
     *
     */
    public function optionsArray()
    {
        if ($this->type == 'select')
            return $this->options()->pluck('text', 'id')->toArray();
        return [];
    }



    /**
     * The "booting" method of the model.
     *
     * Overrides parent function
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        if (Auth::check()) {
            // create a event to happen on creating
            static::creating(function ($table) {
                $table->created_by = Auth::user()->id;
                $table->updated_by = Auth::user()->id;
            });

            // create a event to happen on updating
            static::updating(function ($table) {
                $table->updated_by = Auth::user()->id;
            });
        }
    }
}