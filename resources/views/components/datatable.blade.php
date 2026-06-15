@props(['id' => 'dataTable'])

<table id="{{ $id }}" class="table table-bordered table-striped datatable w-100">
    <thead>
        <tr>
            {{ $head }}
        </tr>
    </thead>
    <tbody>
        {{ $slot }}
    </tbody>
</table>
