<?php

namespace App\Http\Controllers\Ventes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ventes\{tvente_services};
use App\Models\Ventes\{tvente_stock_service};
use App\Traits\{GlobalMethod,Slug};
use DB;

use App\User;
use App\Message;

// tvente_services
// nom_service
// active


class tvente_servicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use GlobalMethod;
    use Slug;
    public function index(Request $request)
    {
        //'id','code','nom_service','compte_fss_bl','active'

        $data = DB::table("tvente_services")
        ->select("tvente_services.id","tvente_services.nom_service","tvente_services.created_at","status",
        'tvente_services.active');

        if (!is_null($request->get('query'))) {
            # code...
            $query = $this->Gquery($request);

            $data->where('tvente_services.nom_service', 'like', '%'.$query.'%')
            ->orderBy("tvente_services.id", "desc");

            return $this->apiData($data->paginate(10));
           

        }
        return $this->apiData($data->paginate(10));
    }


    function fetch_tvente_services_2()
    {
         $data = DB::table("tvente_services")
         ->select("tvente_services.id", "tvente_services.nom_service","tvente_services.created_at","status",
         'tvente_services.active')
        ->get();
        
        return response()->json(['data' => $data]);

    }    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     // tvente_services
// nom_service
// active

    public function store(Request $request)
    {
        //
        if ($request->id !='') 
        {
            # code...
            // update  status
            $data = tvente_services::where("id", $request->id)->update([
                'nom_service' =>  $request->nom_service,
                'status' =>  $request->status
            ]);
            return $this->msgJson('Modification avec succès!!!');

        }
        else
        {
            // insertion 
            $data = tvente_services::create([
                'nom_service' =>  $request->nom_service,
                'status' =>  $request->status,
                'active' =>  $request->active
            ]);

            return $this->msgJson('Insertion avec succès!!!');
        }
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $data = tvente_services::where('id', $id)->get();
        return response()->json(['data' => $data]);
    }


    function insert_data_stock_service($idService)
    {

        $inserteds = DB::table('tvente_produit')->get(); 

            foreach ($inserteds as $data_insert) {   
                
                
                $unitePivot='';
                $qtePivot=0;

                $unites = DB::table('tvente_detail_unite')
                ->join('tvente_unite','tvente_unite.id','=','tvente_detail_unite.refUnite')
                ->select('tvente_detail_unite.id','refProduit','refUnite','puUnite','qteUnite','puBase',
                'qteBase','estunite','estpivot','tvente_detail_unite.active','tvente_detail_unite.author',
                'tvente_detail_unite.refUser','nom_unite','code_unite'
                )
                ->where([
                        ['refProduit', $data_insert->id],          
                        ['tvente_detail_unite.estpivot','OUI']
                ])->first(); 
                if ($unites) {
                    $unitePivot = $unites->nom_unite;            
                    $qtePivot = $unites->qteBase;
                }

                
                $data = tvente_stock_service::create([            
                'refService'       =>  $idService,    
                'refProduit'       =>  $data_insert->id,        
                'pu'       =>  $data_insert->pu,
                'qte'    =>  $data_insert->qte,
                'uniteBase'    =>  $data_insert->uniteBase,
                'cmup'    =>  $data_insert->cmup,
                'devise'    =>  $data_insert->devise,
                'taux'    =>  $data_insert->taux,
                'active'    =>  'OUI',
                'unitePivot'    =>  $unitePivot,
                'qtePivot'    =>  $qtePivot,
                'author'       =>  $data_insert->author,
                'refUser'       =>  $data_insert->refUser
            ]);


        }
        return response()->json([
            'data'  =>  "suppression avec succès",
        ]);
        
    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = tvente_services::where('id', $id)->delete();
        return $this->msgJson('Suppression avec succès!!!');
    }

    public function destroyMessage($id)
    {
        //
        $data = Message::where('id', $id)->delete();
        return $this->msgJson('Suppression avec succès!!!');
    }
}
