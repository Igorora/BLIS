<?php

class Icd10Controller extends BaseController {



	public function getDiags($term = '')
	{
		return static::makeQuery('Diag', $term);
	}
	public function getMeds($term = '')
	{
		return static::makeQuery('Medicine', $term);
	}
	public function getSymps($term = '')
	{
		return static::makeQuery('Symptom', $term);
	}
	public function getSigns($term = '')
	{
		return static::makeQuery('Sign', $term);
	}

	/**
	 * Make a full text search query
	 *
	 * @param String $modelName Elloquent model name
	 * @param String $searchTerm term to search for
	 * @return Json Json formatted result of query
	 */
	private static function makeQuery($modelName, $searchTerm)
	{
		$columns = [
			'id' => 'id',
			'text' => 'text',
			'score' => DB::raw('(match (id) against (\''.$searchTerm.'*'.'\' in boolean mode)) as score')
		];
		$results = $modelName::select($columns)
			->whereRaw('match (id) against (\''.$searchTerm.'*'.'\' in boolean mode)')
			->orderBy('score', 'desc')
			->get()
			->toJson();
		return $results;
	}

}
