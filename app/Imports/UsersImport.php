<?php

namespace App\Imports;

use App\AssociationBenefit;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AssociationBenefit([
            'name'            => $row[0],
            'phone_number'    => $row[1],
            'identify_number' => $row[2],
        ]);
    }
}
