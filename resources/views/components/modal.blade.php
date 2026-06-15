@props(['id' => 'confirmModal', 'title' => 'Xác nhận', 'body' => 'Bạn có chắc chắn?'])

<div class="modal fade" id="{{ $id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">{{ $body }}</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="{{ $id }}Confirm">Xác nhận</button>
            </div>
        </div>
    </div>
</div>
