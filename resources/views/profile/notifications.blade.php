@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Tùy chọn thông báo" icon="bell">
                <form action="{{ route('profile.notifications.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1"
                                {{ old('email_notifications', $user->notification_preferences['email_notifications'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_notifications">
                                <strong>Đưa thé thông báo</strong>
                                <br>
                                <small class="text-muted">Nhận thông báo dự kiến trải hạn đăt phòng, thanh toán v.v.</small>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" value="1"
                                {{ old('sms_notifications', $user->notification_preferences['sms_notifications'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="sms_notifications">
                                <strong>Thông báo SMS</strong>
                                <br>
                                <small class="text-muted">Đưa thông báo quản lý đến số điện thoại của bạn</small>
                            </label>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3"><strong>Loại thông báo</strong></h6>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="booking_notifications" name="booking_notifications" value="1"
                                {{ old('booking_notifications', $user->notification_preferences['booking_notifications'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="booking_notifications">
                                Đặt phòng (mới, thay đổi, hủy)
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="payment_notifications" name="payment_notifications" value="1"
                                {{ old('payment_notifications', $user->notification_preferences['payment_notifications'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="payment_notifications">
                                Thanh toán (chìk-in, hóa đơn, hoàn tiền)
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="system_notifications" name="system_notifications" value="1"
                                {{ old('system_notifications', $user->notification_preferences['system_notifications'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="system_notifications">
                                Thông báo hệ thống (bảo trì, cập nhật)
                            </label>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu tùy chọn
                        </button>
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                            Hủy bỏ
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection