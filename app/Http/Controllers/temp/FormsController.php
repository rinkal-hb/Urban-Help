<?php

namespace App\Http\Controllers\temp;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class FormsController extends Controller
{
    public function form_inputs()
    {
        return view('pages.form-inputs');
    }

    public function form_check_radios()
    {
        return view('pages.form-check-radios');
    }

    public function form_input_groups()
    {
        return view('pages.form-input-groups');
    }

    public function form_select()
    {
        return view('pages.form-select');
    }

    public function form_range()
    {
        return view('pages.form-range');
    }

    public function form_input_masks()
    {
        return view('pages.form-input-masks');
    }

    public function form_file_uploads()
    {
        return view('pages.form-file-uploads');
    }

    public function form_datetime_pickers()
    {
        return view('pages.form-datetime-pickers');
    }

    public function form_color_pickers()
    {
        return view('pages.form-color-pickers');
    }

    public function floating_labels()
    {
        return view('pages.floating-labels');
    }

    public function form_layouts()
    {
        return view('pages.form-layouts');
    }

    public function quill_editor()
    {
        return view('pages.quill-editor');
    }

    public function form_validations()
    {
        return view('pages.form-validations');
    }

    public function form_select2()
    {
        return view('pages.form-select2');
    }

}
