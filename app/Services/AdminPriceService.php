<?php


namespace App\Services;


use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Boolean;

class AdminPriceService
{
    public function priceValidate($company){
        $rule =  Validator::extend('uniqueOriginAndDestination', function ($attribute, $value, $parameters, $validator) use ($company) {
            $count = Price::where('origin_location', $value)
                ->where('destination_location', $parameters[0]);
                if($company){
                    $count->where('company', $company);
                }
            return $count->where('isRegularPrice', $company ? false : true)->count() === 0;
        },'Lokasi Asal Dan Tujuan Pengiriman Sudah Ada');

        return $rule;
    }

    public function getRegularPrice($origin, $destination, $isCorporateRegular){


        $price = Price::with('originLocation.city.province','destinationLocation.city.province')
            ->where('origin_location',$origin)
            ->where('destination_location',$destination)
            ->where('isRegularPrice',true);
        return $price->first();

    }

    public function getCorporatePrice(Request $request){
        $origin = request('origin');
        $destination = request('destination');
        $data = Price::with('originLocation.city.province','destinationLocation.city.province')
            ->where('origin_location',$origin)
            ->where('destination_location',$destination)
            ->where('company',1)
            ->first();

        return $data;
    }


}
