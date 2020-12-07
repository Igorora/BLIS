<table class="table table-condensed table-bordered">
    <thead>
      <tr>
        <th>Test</th>
        <th>Transmitted to</th>
        <th>Designation</th>
		<th>Time transmitted</th>
		<th>Transmitted by</th>
      </tr>
    </thead>
    <tbody>
      @forelse($transmittedResults as $transmittedResult)
	  <tr>
        <td>{{$transmittedResult->test->testType->name}}</td>
        <td>{{$transmittedResult->transmitted_to}}</td>
        <td>{{$transmittedResult->designation}}</td>
		<td>{{$transmittedResult->time_transmitted}}</td>
		<td>{{$transmittedResult->transmittedBy->name}}</td>
      </tr>
      @empty
      <tr>
        <td colspan="5">None of the tests has been transmitted yet</td>
       </tr>
	  @endforelse
	
	</tbody>
  </table>