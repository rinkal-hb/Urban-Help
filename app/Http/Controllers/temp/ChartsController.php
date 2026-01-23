<?php

namespace App\Http\Controllers\temp;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ChartsController extends Controller
{
    public function apex_line_charts()
    {
        return view('pages.apex-line-charts');
    }

    public function apex_area_charts()
    {
        return view('pages.apex-area-charts');
    }

    public function apex_column_charts()
    {
        return view('pages.apex-column-charts');
    }
    public function apex_bar_charts()
    {
        return view('pages.apex-bar-charts');
    }
    public function apex_mixed_charts()
    {
        return view('pages.apex-mixed-charts');
    }
    public function apex_rangearea_charts()
    {
        return view('pages.apex-rangearea-charts');
    }
    public function apex_timeline_charts()
    {
        return view('pages.apex-timeline-charts');
    }
    public function apex_candlestick_charts()
    {
        return view('pages.apex-candlestick-charts');
    }

    public function apex_boxplot_charts()
    {
        return view('pages.apex-boxplot-charts');
    }

    public function apex_bubble_charts()
    {
        return view('pages.apex-bubble-charts');
    }

    public function apex_scatter_charts()
    {
        return view('pages.apex-scatter-charts');
    }

    public function apex_heatmap_charts()
    {
        return view('pages.apex-heatmap-charts');
    }

    public function apex_treemap_charts()
    {
        return view('pages.apex-treemap-charts');
    }

    public function apex_pie_charts()
    {
        return view('pages.apex-pie-charts');
    }

    public function apex_radialbar_charts()
    {
        return view('pages.apex-radialbar-charts');
    }

    public function apex_radar_charts()
    {
        return view('pages.apex-radar-charts');
    }

    public function apex_polararea_charts()
    {
        return view('pages.apex-polararea-charts');
    }

    public function chartjs_charts()
    {
        return view('pages.chartjs-charts');
    }

    public function echarts()
    {
        return view('pages.echarts');
    }

    public function chartjs()
    {
        return view('pages.chartjs');
    }

    public function echartjs()
    {
        return view('pages.echartjs');
    }

}
