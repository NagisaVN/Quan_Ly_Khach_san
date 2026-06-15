<?php
$base = dirname(__DIR__);
$files = [];

$files['app/Exceptions/RoomNotAvailableException.php'] = '<?php
namespace App\Exceptions;
class RoomNotAvailableException extends \Exception {
    public function __construct(string $message = "Phòng đã được đặt trong khoảng thời gian này.") { parent::__construct($message); }
}';

$files['app/Exceptions/RoomMaintenanceException.php'] = '<?php
namespace App\Exceptions;
class RoomMaintenanceException extends \Exception {
    public function __construct(string $message = "Không thể check-in: phòng đang bảo trì.") { parent::__construct($message); }
}';

$files['app/Exceptions/PaymentRequiredException.php'] = '<?php
namespace App\Exceptions;
class PaymentRequiredException extends \Exception {
    public function __construct(string $message = "Vui lòng thanh toán đủ trước khi check-out.") { parent::__construct($message); }
}';

$files['app/Models/BookingHistory.php'] = '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BookingHistory extends Model {
    protected $fillable = ["booking_id","user_id","action","from_status","to_status","changes","notes"];
    protected function casts(): array { return ["changes" => "array"]; }
    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}';

$files['app/Models/ServiceCategory.php'] = '<?php
namespace App\Models;
use App\Traits\BelongsToBranch; use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\HasMany;
class ServiceCategory extends Model {
    use BelongsToBranch, HasAuditColumns;
    protected $fillable = ["branch_id","name","code","description","sort_order","is_active"];
    protected function casts(): array { return ["is_active"=>"boolean"]; }
    public function services(): HasMany { return $this->hasMany(Service::class); }
}';

$files['app/Models/Service.php'] = '<?php
namespace App\Models;
use App\Traits\BelongsToBranch; use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Service extends Model {
    use BelongsToBranch, HasAuditColumns;
    protected $fillable = ["branch_id","service_category_id","name","code","description","unit_price","unit","is_active"];
    protected function casts(): array { return ["unit_price"=>"decimal:2","is_active"=>"boolean"]; }
    public function category(): BelongsTo { return $this->belongsTo(ServiceCategory::class, "service_category_id"); }
}';

$files['app/Models/PricingRule.php'] = '<?php
namespace App\Models;
use App\Traits\BelongsToBranch; use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PricingRule extends Model {
    use BelongsToBranch, HasAuditColumns;
    protected $fillable = ["branch_id","room_type_id","name","type","conditions","adjustment_type","value","priority","valid_from","valid_to","is_active"];
    protected function casts(): array { return ["conditions"=>"array","value"=>"decimal:2","valid_from"=>"date","valid_to"=>"date","is_active"=>"boolean"]; }
    public function roomType(): BelongsTo { return $this->belongsTo(RoomType::class); }
}';

foreach ($files as $path => $content) {
    $full = $base . "/" . str_replace("/", DIRECTORY_SEPARATOR, $path);
    if (!is_dir(dirname($full))) mkdir(dirname($full), 0777, true);
    file_put_contents($full, $content);
    echo "Wrote $path\n";
}
echo "Done batch 1\n";
