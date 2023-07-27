<?php

namespace App\Exports;

use App\Models\Delivery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DeliveryExport implements FromQuery, WithHeadings
{
    use Exportable;

    public function __construct( $unique_id)
    {
        $this->unique_id = $unique_id;
    }

    public function query()
    {
        $deliveries = Delivery::query()->where('unique_id', $this->unique_id)->orderBy('BTPLLijnnr');

        return $deliveries;
    }

    public function headings(): array
    {
        return [
            "ID",
            "UniqueId",
            "SupplierCode",
            "UserId",
            "UserName",
            "BTPLArticleCode",
            "BTPLTekst",
            "BTPLVerpakkingsCode",
            "BTPLKaliber",
            "BTPLOrderDeliveryDate",
            "BTPLOrderDeliveryAt",
            "BTPLArticleRemark",
            "BTPLOrderReference",
            "BTPLArticleCollie",
            "BTPLArticleWeight",
            "CreatedAt",
            "UpdatedAt",
            "BTPLLijnnr"
        ];
    }

}
