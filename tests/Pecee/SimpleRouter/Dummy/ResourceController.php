<?php
class ResourceController implements \Simple\Controllers\IResourceController
{

    public function index(): ?string
    {
        return 'index';
    }

    public function show($id): ?string
    {
        return 'show ' . $id;
    }

    public function store($request): ?string
    {
        return 'store';
    }

    public function create(): ?string
    {
        return 'create';
    }

    public function edit($id): ?string
    {
        return 'edit ' . $id;
    }

    public function update($request, $id): ?string
    {
        return 'update ' . $id;
    }

    public function destroy($id): ?string
    {
        return 'destroy ' . $id;
    }
}
