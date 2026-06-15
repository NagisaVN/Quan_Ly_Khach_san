@extends('layouts.app')
@section('title', 'Sửa phòng')
@section('page-title', 'Sửa phòng')
@section('content')
<x-adminlte-card>
<form method="POST" action="{{ route('rooms.rooms.update', $room) }}">@csrf @method('PUT')
<div class="row g-3">
<div class="col-md-4"><label class="form-label">Số phòng *</label><input type="text" name="room_number" class="form-control" value="{{ old('room_number', $room->room_number) }}" required></div>
<div class="col-md-4"><label class="form-label">Tầng *</label><select name="floor_id" class="form-select select2" required>@foreach($floors as $f)<option value="{{ $f->id }}" @selected(old('floor_id', $room->floor_id)==$f->id)>{{ $f->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Loại phòng *</label><select name="room_type_id" class="form-select select2" required>@foreach($roomTypes as $t)<option value="{{ $t->id }}" @selected(old('room_type_id', $room->room_type_id)==$t->id)>{{ $t->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Trạng thái *</label><select name="status" class="form-select" required>@foreach($statuses as $s)<option value="{{ $s->value }}" @selected(old('status', $room->status->value)==$s->value)>{{ $s->value }}</option>@endforeach</select></div>
<div class="col-md-8"><label class="form-label">Tiện ích</label><select name="amenity_ids[]" class="form-select select2" multiple>@foreach($amenities as $a)<option value="{{ $a->id }}" @selected(collect(old('amenity_ids', $room->amenities->pluck('id')))->contains($a->id))>{{ $a->name }}</option>@endforeach</select></div>
<div class="col-12"><label class="form-label">Ghi chú</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $room->notes) }}</textarea></div>
<div class="col-md-6"><div class="form-check mt-2"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $room->is_active))><label class="form-check-label">Hoạt động</label></div></div>
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary">Cập nhật</button><a href="{{ route('rooms.rooms.show', $room) }}" class="btn btn-secondary">Hủy</a></div>
</form>
</x-adminlte-card>
@endsection
