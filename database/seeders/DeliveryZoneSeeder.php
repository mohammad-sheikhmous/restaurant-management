<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            [
                'name' => ['en' => 'Hamak Area', 'ar' => 'منطقة الهمك'],
                'coordinates' => json_encode([
                    ["lat" => 33.498410082785924, "lng" => 36.30569393060121],
                    ["lat" => 33.491860785822475, "lng" => 36.30565101525697],
                    ["lat" => 33.49139551637209, "lng" => 36.32230216882142],
                    ["lat" => 33.49440182870851, "lng" => 36.32311756036195],
                    ["lat" => 33.50370641960514, "lng" => 36.317023581480115]
                ]),
            ],
            [
                'name' => ['en' => 'Mazza Area', 'ar' => 'منطقة المزة'],
                'coordinates' => json_encode([
                    ["lat" => 33.48887390959424, "lng" => 36.22821916774772],
                    ["lat" => 33.48980447309198, "lng" => 36.23611559108756],
                    ["lat" => 33.49739177149872, "lng" => 36.24607195095084],
                    ["lat" => 33.50476369811018, "lng" => 36.2546550197985],
                    ["lat" => 33.50304602373959, "lng" => 36.25843157009147],
                    ["lat" => 33.50691074313596, "lng" => 36.26538385585807],
                    ["lat" => 33.51106154581379, "lng" => 36.26263727382682],
                    ["lat" => 33.5112762370885, "lng" => 36.272250310936194],
                    ["lat" => 33.51428185900099, "lng" => 36.27688516811393],
                    ["lat" => 33.515856190714025, "lng" => 36.26212228969596],
                    ["lat" => 33.51399561379341, "lng" => 36.261950628319006],
                    ["lat" => 33.51428185900099, "lng" => 36.25791658596061],
                    ["lat" => 33.51034590438436, "lng" => 36.24984850124381],
                    ["lat" => 33.510846854006424, "lng" => 36.246758596458655],
                    ["lat" => 33.50941556167761, "lng" => 36.24358286098502],
                    ["lat" => 33.49982529241775, "lng" => 36.227790014305334]
                ]),
            ],
            [
                'name' => ['en' => 'Damascus Center Area', 'ar' => 'منطقة مركز دمشق'],
                'coordinates' => json_encode([
                    ["lat" => 33.5354102352186, "lng" => 36.32370471276465],
                    ["lat" => 33.52739696916632, "lng" => 36.307911866084964],
                    ["lat" => 33.526109053568106, "lng" => 36.301646225826175],
                    ["lat" => 33.52875641482164, "lng" => 36.29383563317481],
                    ["lat" => 33.521100310573786, "lng" => 36.2825059822959],
                    ["lat" => 33.51766557630399, "lng" => 36.27778529442969],
                    ["lat" => 33.51551879812881, "lng" => 36.280446045772464],
                    ["lat" => 33.517951809368924, "lng" => 36.28602504052344],
                    ["lat" => 33.51258478180338, "lng" => 36.28885745324317],
                    ["lat" => 33.50979379589091, "lng" => 36.288943283931644],
                    ["lat" => 33.507933088603224, "lng" => 36.28902911462012],
                    ["lat" => 33.507861522139315, "lng" => 36.292891495601566],
                    ["lat" => 33.49970255731338, "lng" => 36.2920331887168],
                    ["lat" => 33.49891524809152, "lng" => 36.30104541100684],
                    ["lat" => 33.5053566586165, "lng" => 36.315121643916996],
                    ["lat" => 33.51194071612162, "lng" => 36.318898194209964],
                    ["lat" => 33.51830959936821, "lng" => 36.32018565453711],
                    ["lat" => 33.523819378477675, "lng" => 36.31846904076758],
                    ["lat" => 33.529543452537844, "lng" => 36.32344722069922]
                ]),
            ],
            [
                'name' => ['en' => 'Middan Area', 'ar' => 'منطقة الميدان'],
                'coordinates' => json_encode([
                    ["lat" => 33.492089682103376, "lng" => 36.28869353104767],
                    ["lat" => 33.47920440749072, "lng" => 36.28217039872345],
                    ["lat" => 33.46416916455246, "lng" => 36.27702055741486],
                    ["lat" => 33.46517159523686, "lng" => 36.28251372147736],
                    ["lat" => 33.470183574762686, "lng" => 36.286805255901186],
                    ["lat" => 33.48034984285675, "lng" => 36.29161177445587],
                    ["lat" => 33.48092255486037, "lng" => 36.298821552287905],
                    ["lat" => 33.47132912938735, "lng" => 36.29796324540314],
                    ["lat" => 33.46803561902703, "lng" => 36.29864989091095],
                    ["lat" => 33.46817881773229, "lng" => 36.31461439896759],
                    ["lat" => 33.471042742150836, "lng" => 36.31598768998322],
                    ["lat" => 33.47519526444568, "lng" => 36.30791960526642],
                    ["lat" => 33.47519526444568, "lng" => 36.316502674114076],
                    ["lat" => 33.47877486532391, "lng" => 36.31615935136017],
                    ["lat" => 33.47719985915804, "lng" => 36.32439909745392],
                    ["lat" => 33.479061227005104, "lng" => 36.32611571122345],
                    ["lat" => 33.48421557540222, "lng" => 36.31736098099884],
                    ["lat" => 33.48378605808325, "lng" => 36.30414305497345],
                    ["lat" => 33.488796960941194, "lng" => 36.30517302323517],
                    ["lat" => 33.48922645341046, "lng" => 36.296074970256655],
                    ["lat" => 33.4882243010021, "lng" => 36.29092512894806]
                ]),
            ],
        ];
        foreach ($zones as $zone)
            DeliveryZone::create($zone);
    }
}
