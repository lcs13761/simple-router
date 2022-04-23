<?php

namespace Simple\Controllers;

interface IResourceController
{

    /**
     * @return mixed
     */
    public function index();

    /**
     * @param mixed $id
     * @return mixed
     */
    public function show($id);

    /**
     * @param mixed $request
     * @return mixed
     */
    public function store($request);

    /**
     * @return mixed
     */
    public function create();

    /**
     * View
     * @param mixed $id
     * @return mixed
     */
    public function edit($id);

    /**
     * @param mixed $request
     * @param mixed $id
     * @return mixed
     */
    public function update($request,$id);

    /**
     * @param mixed $id
     * @return mixed
     */
    public function destroy($id);

}