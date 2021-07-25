<?php

namespace App\Exports;

use App\AssociationBenefit;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AssociationBenefit::select('name' , 'phone_number' , 'identify_number')
            ->get();
    }
}
