<?php

class TestStatusController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function notifyTestStatuses()
	{
		$statuses=[Test::NOT_RECEIVED,Test::PENDING,Test::STARTED,Test::COMPLETED,Test::VERIFIED,null];
		$statusCounts=[];
		//$labSectionIp=Input::get('labSectionIp');


		for ($i = 0; $i < count($statuses); $i++) {
			$statusCounts[$i]=Ip::TestCountByStatus(Request::ip(),$statuses[$i]);

		}

		return $statusCounts;
		
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
