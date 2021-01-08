<?php

class Icd10Controller extends BaseController {



	public function getDiags($term = '')
	{
		$diags = Diag::where('id','LIKE','%' .$term. '%')
					->orWhere('text', 'LIKE', '%' .$term. '%')
					->orderBy('text', 'ASC')
					->get()
					->toJson();

		return $diags;
	}
	public function getMeds($term = '')
	{
		$meds = Medicine::where('id','LIKE','%' .$term. '%')
					->orWhere('text', 'LIKE', '%' .$term. '%')
					->orderBy('text', 'ASC')
					->get();
		return $meds->toJson();//json_encode(['results' => $meds->toJson(), 'total' => $meds->count()]);
	}
	public function getSymps($term = '')
	{
		$symps = Symptom::where('id','LIKE','%' .$term. '%')
					->orWhere('text', 'LIKE', '%' .$term. '%')
					->orderBy('text', 'ASC')
					->get()
					->toJson();

		return $symps;
	}
	public function getSigns($term = '')
	{
		$signs = Sign::where('id','LIKE','%' .$term. '%')
					->orWhere('text', 'LIKE', '%' .$term. '%')
					->orderBy('text', 'ASC')
					->get()
					->toJson();

		return $signs;
	}

}
