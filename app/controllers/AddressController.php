<?php
use Illuminate\Database\QueryException;

class AddressController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function countries()
	{
            return Country::orderBy('name', 'ASC')->get();
            
         
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function provinces($id)
	{
             
        $provinces = Province::where('country_id','=',$id)->get();
		if($provinces->count()>0){
			$prov_options='<option value="">Select province</option>';
			$prov_options .='<option value="Not provided">Not provided</option>';
			foreach($provinces as $province){
				$prov_options .= '<option data-province="'.$province->id .'" value="'. $province->name .'">'. $province->name .'</option>';												
			}
			
		}else{
			$prov_options='<option value="">Province not available</option>';											
		}
		return $prov_options;
             
           
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function districts($id)
	{
        $districts= District::where('province_id','=', $id)->get();
		if($districts->count()>0){
		$distr_options='<option value="">Select district</option>';
		$distr_options .='<option value="Not provided">Not provided</option>';
		foreach($districts as $district){
				$distr_options .= '<option data-district="'.$district->id .'" value="'. $district->name .'">'. $district->name .'</option>';												
			}
																				
		}else{
			$distr_options='<option value="">District not available</option>';											
			}
																			
		return $distr_options;
													
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function sectors($id)
	{
        $sectors= Sector::where('district_id','=', $id)->get();
		if($sectors->count()>0){
		$sect_options='<option value="">Select sector</option>';
		$sect_options .='<option value="Not provided">Not provided</option>';
		foreach($sectors as $sector){
			$sect_options .= '<option data-sector="'.$sector->id .'" value="'. $sector->name .'">'. $sector->name .'</option>';												
			}
																				
		}else{
			$sect_options='<option value="">Sector not available</option>';											
		}
																			
		return $sect_options;
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function cells($id)
	{
        $cells= Cell::where('sector_id','=', $id)->get();
		if($cells->count()>0){
		$cell_options='<option value="">Select cell</option>';
		$cell_options .='<option value="Not provided">Not provided</option>';
		foreach($cells as $cell){
		$cell_options .= '<option data-cell="'.$cell->id .'" value="'. $cell->name .'">'. $cell->name .'</option>';												
								}
			
		}else{
			$cell_options='<option value="">Cell not available</option>';											
		}
											
		return $cell_options;
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function villages($id)
	{
        $villages= Village::where('cell_id','=', $id)->get();
		if($villages->count()>0){
		$vill_options='<option value="">Select Village</option>';
		$vill_options .='<option value="Not provided">Not provided</option>';
		foreach($villages as $village){
			$vill_options .= '<option data-village="'.$village->id .'" value="'. $village->name .'">'. $village->name .'</option>';												
		}
		
	}else{
		$vill_options='<option value="">Village not available</option>';											
	}
								
	return $vill_options;
	//
	}



}
