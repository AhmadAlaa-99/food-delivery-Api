<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Zone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers;
use Grimzy\LaravelMysqlSpatial\Types\Point;


class ConfigController extends Controller
{
    public function geocode_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $request->trim->lat . ',' . $request->trim->lng . '&key=' . 'AIzaSyDO-vsupaW-cSjcBbd9nfPhtAvJsHwaC9o');
        return $response->json();
    }
    public function get_zone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $point = new Point($request->lat, $request->lng);
        $zones = Zone::contains('coordinates', $point)->latest()->get();
        /* if(count($zones)<1)
        {
            return response()->json(['message'=>trans('messages.service_not_available_in_this_area_now')], 404);
        }
        foreach($zones as $zone)
        {
            if($zone->status)
            {
                return response()->json(['zone_id'=>$zone->id], 200);
            }
        }*/
        //return response()->json(['message'=>trans('messages.we_are_temporarily_unavailable_in_this_area')], 403);
        return response()->json(['zone_id' => 1], 200);
    }
    // public response()->json([
    //     'business'=>BusinessSetting::where(['key'=>'business_name'])->first(),
    //     'base_urls'=>[
    //         'customer_image_url'=>asset('storage/profile'),
    //         'business_logo_url'=>asset('storage/business')
    //     ],
    //     'country'=>BusinessSetting::where(['key'=>'country'])->first()->value,
    //     'default_location'

    // ])
    public function place_api_autocomplete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_text' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(

                ['errors' => Helpers::error_processor($validator)],
                403
            );
        }
        $response = Http::get(
            'https://maps.googleapis.com/maps/api/place/autocomplete/json?input='
                . $request['search_text']
                . '&key='
                . 'AIzaSyDO-vsupaW-cSjcBbd9nfPhtAvJsHwaC9o'
        );
        return $response->json();
    }
    public function place_api_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'placeid' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(

                ['errors' => Helpers::error_processor($validator)],
                403
            );
        }
        $response = Http::get(
            'https://maps.googleapis.com/maps/api/place/details/json?placeid='
                . $request['placeid']
                . '&key='
                . 'AIzaSyDO-vsupaW-cSjcBbd9nfPhtAvJsHwaC9o'
        );
        return $response->json();
    }
}
