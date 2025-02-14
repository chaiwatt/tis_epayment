<?php

namespace App\Http\Controllers\Basic;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Basic\StatusOperation as status_operation;
use Illuminate\Http\Request;

class StatusOperationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */

    public function index(Request $request)
    {
        $model = str_slug('status_operation','-');
        if(auth()->user()->can('view-'.$model)) {

            $keyword = $request->get('search');
            $filter = [];
            $filter['filter_state'] = $request->get('filter_state', '');
            $filter['perPage'] = $request->get('perPage', 10);

            $Query = new status_operation;

            if ($filter['filter_state']!='') {
                $Query = $Query->where('state', $filter['filter_state']);
            }

            $status_operation = $Query->sortable()->with('user_created')
                                                  ->with('user_updated')
                                                  ->paginate($filter['perPage']);

            return view('basic.status_operation.index', compact('status_operation', 'filter'));
        }
        abort(403);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $model = str_slug('status_operation','-');
        if(auth()->user()->can('add-'.$model)) {
            return view('basic.status_operation.create');
        }
        abort(403);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $model = str_slug('status_operation','-');
        if(auth()->user()->can('add-'.$model)) {
            $this->validate($request, [
			'title' => 'required'
		]);
            $request->request->add(['created_by' => auth()->user()->getKey()]); //user create
            $requestData = $request->all();

            status_operation::create($requestData);
            return redirect('basic/status_operation')->with('flash_message', 'เพิ่ม status_operation เรียบร้อยแล้ว');
        }
        abort(403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $model = str_slug('status_operation','-');
        if(auth()->user()->can('view-'.$model)) {
            $status_operation = status_operation::findOrFail($id);
            return view('basic.status_operation.show', compact('status_operation'));
        }
        abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $model = str_slug('status_operation','-');
        if(auth()->user()->can('edit-'.$model)) {
            $status_operation = status_operation::findOrFail($id);
            return view('basic.status_operation.edit', compact('status_operation'));
        }
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $model = str_slug('status_operation','-');
        if(auth()->user()->can('edit-'.$model)) {
            $this->validate($request, [
			'title' => 'required'
		]);
            $request->request->add(['updated_by' => auth()->user()->getKey()]); //user update
            $requestData = $request->all();

            $status_operation = status_operation::findOrFail($id);
            $status_operation->update($requestData);

            return redirect('basic/status_operation')->with('flash_message', 'แก้ไข status_operation เรียบร้อยแล้ว!');
        }
        abort(403);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id, Request $request)
    {
        $model = str_slug('status_operation','-');
        if(auth()->user()->can('delete-'.$model)) {

          $requestData = $request->all();

          if(array_key_exists('cb', $requestData)){
            $ids = $requestData['cb'];
            $db = new status_operation;
            status_operation::whereIn($db->getKeyName(), $ids)->delete();
          }else{
            status_operation::destroy($id);
          }

          return redirect('basic/status_operation')->with('flash_message', 'ลบข้อมูลเรียบร้อยแล้ว!');
        }
        abort(403);

    }


    /*
      **** Update State ****
    */
    public function update_state(Request $request){

      $model = str_slug('status_operation','-');
      if(auth()->user()->can('edit-'.$model)) {

        $requestData = $request->all();

        if(array_key_exists('cb', $requestData)){
          $ids = $requestData['cb'];
          $db = new status_operation;
          status_operation::whereIn($db->getKeyName(), $ids)->update(['state' => $requestData['state']]);
        }

        return redirect('basic/status_operation')->with('flash_message', 'แก้ไขข้อมูลเรียบร้อยแล้ว!');
      }

      abort(403);

    }

    /*
      **** Update Budget State ****
    */
    public function update_budget_state(Request $request){

      $model = str_slug('status_operation','-');
      if(auth()->user()->can('edit-'.$model)) {

        $requestData = $request->all();

        if(array_key_exists('cb', $requestData)){
          $ids = $requestData['cb'];
          $db = new status_operation;
          status_operation::whereIn($db->getKeyName(), $ids)->update(['budget_state' => $requestData['budget_state']]);
        }

        return redirect('basic/status_operation')->with('flash_message', 'แก้ไขข้อมูลเรียบร้อยแล้ว!');
      }

      abort(403);

    }

}
