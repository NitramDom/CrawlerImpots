<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;

Route::get('/', function () {
    return view('crawler');
});

Route::post('/', function (Request $request) {

    $number = $request->number;
    $reference = $request->reference;

    $crawler = Goutte::request('GET', 'https://cfsmsp.impots.gouv.fr/secavis/faces/commun/index.jsf');
    $viewState = $crawler->filterXPath('//*[@name="javax.faces.ViewState"]/@value')->first()->text();
    $form = $crawler->selectButton('Valider')->form();
    $crawler = Goutte::submit($form, [
        'j_id_7:spi' => $number,
        'j_id_7:num_facture' => $reference,
        'javax.faces.ViewState' => $viewState
    ]);

    if ($crawler->filter('#nonTrouve')->count() >= 1) {
        return response()->json(["status" => "error", "message" => $crawler->filter('#nonTrouve')->first()->text()]);
    }

    $response = $crawler->filter('td')->each(function ($node, $i) {
        return $node->text();
    });

    $data = [
        "person_1" => [
            "lastname" => $response[4], //Nom
            "birth_name" => $response[7], //Nom de naissance
            "firstname" => $response[10], //Prénom(s)
            "birthdate" => $response[13], //Date de naissance
            "address_line_1" => $response[16], //Adresse 1
            "address_line_2" => $response[19], //Adresse 2
            "address_line_3" => $response[21], //Adresse 3
        ],
        "person_2" => [
            "lastname" => $response[5], //Nom
            "birth_name" => $response[8], //Nom de naissance
            "firstname" => $response[11], //Prénom(s)
            "birthdate" => $response[14], //Date de naissance
            "address_line_1" => $response[17], //Adresse 1
            "address_line_2" => $response[20], //Adresse 2
            "address_line_3" => $response[22], //Adresse 3
        ],
        "infos" => [
            "collection_date_tax_notice" => $response[24], //Date de mise en recouvrement de l'avis d'impôt
            "establishment_date" => $response[26], //Date d'établissement
            "parts_number" => $response[28], //Nombre de part(s)
            "family_situation" => $response[30], //Situation de famille
            "dependents_number" => $response[32], //Nombre de personne(s) à charge
            "total_gross_income" => $response[34], //Revenu brut global
            "taxable_income" => $response[36], //Revenu imposable
            "net_income_before_adjustment" => $response[38], //Impôt sur le revenu net avant correction
            "tax_amount" => $response[40], //Montant de l'impôt
            "tax_reference_income" => $response[42], //Revenu fiscal de référence
        ]
    ];

    return response()->json(["status" => "success", "message" => $data]);
})->name('form');
