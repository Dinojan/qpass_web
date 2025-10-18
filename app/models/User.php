<?php
namespace App\Models;

use PDO;
use App\Core\Lib\Model;
use App\Enums\Status;
use App\Core\Lib\QueryBuilder;

class User extends Model
{
    protected $table = 'users';
    
    protected $fillable = [
        'first_name', 'last_name', 'email', 'username', 'password', 'phone', 
        'address', 'roles', 'device_token', 'web_token', 'status', 
        'country_code', 'country_code_name'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'options' => 'array',
    ];

    protected $appends = ['myrole', 'name', 'images', 'my_status', 'country_code_with_phone'];

    /**
     * Find a user by ID
     */
    public static function find($id)
    {
        $stmt = db()->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userData) {
            return static::createInstance($userData);
        }
        
        return null;
    }

    /**
     * Create a new user instance from array data
     */
    protected static function createInstance($data)
    {
        $user = new static();
        $user->fill($data);
        return $user;
    }

    /**
     * Fill the model with an array of attributes.
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable) || in_array($key, ['id', 'email_verified_at', 'remember_token'])) {
                $this->$key = $value;
            }
        }
        return $this;
    }

/**
 * Query builder for WHERE conditions - Fixed version
 */
public static function where($column, $operator = null, $value = null)
{
    // Handle array of conditions
    if (is_array($column)) {
        $conditions = [];
        $bindings = [];
        
        foreach ($column as $key => $val) {
            $conditions[] = "$key = :$key";
            $bindings[":$key"] = $val;
        }
        
        $query = "SELECT * FROM users WHERE " . implode(' AND ', $conditions);
        $stmt = db()->prepare($query);
        $stmt->execute($bindings);
        
        $users = [];
        while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = static::createInstance($userData);
        }
        
        return $users;
    } 
    // Handle single condition
    else {
        // If only two parameters are passed, assume operator is '='
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $query = "SELECT * FROM users WHERE $column $operator :value";
        $stmt = db()->prepare($query);
        $stmt->execute([':value' => $value]);
        
        $users = [];
        while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = static::createInstance($userData);
        }
        
        return $users;
    }
}

/**
 * Get the first user matching WHERE conditions
 */
public static function firstWhere($column, $operator = null, $value = null)
{
    $users = static::where($column, $operator, $value);
    return !empty($users) ? $users[0] : null;
}

    /**
     * Get the first user matching the conditions
     */
    public static function first()
    {
        $stmt = db()->prepare("SELECT * FROM users LIMIT 1");
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $userData ? static::createInstance($userData) : null;
    }

 

     /**
     * Verify password
     */
    public function verifyPassword($password)
    {
        return password_verify($password, $this->password);
    }

    /**
     * Create a new user
     */
    public static function create(array $data)
    {
        // Filter only fillable attributes
        $fillableData = array_intersect_key($data, array_flip((new static())->fillable));
        
        // Hash password if present
        if (isset($fillableData['password'])) {
            $fillableData['password'] = password_hash($fillableData['password'], PASSWORD_DEFAULT);
        }
        
        // Set default status if not provided
        if (!isset($fillableData['status'])) {
            $fillableData['status'] = Status::ACTIVE;
        }
        
        $columns = implode(', ', array_keys($fillableData));
        $placeholders = ':' . implode(', :', array_keys($fillableData));
        
        $query = "INSERT INTO users ($columns) VALUES ($placeholders)";
        $stmt = db()->prepare($query);
        
        // Bind parameters
        foreach ($fillableData as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        if ($stmt->execute()) {
            $data['id'] = db()->lastInsertId();
            return static::createInstance($data);
        }
        
        return null;
    }

    /**
     * Update the user
     */
    public function update(array $data = [])
    {
        if (!empty($data)) {
            $this->fill($data);
        }
        
        // Filter only fillable attributes
        $fillableData = array_intersect_key(get_object_vars($this), array_flip($this->fillable));
        
        if (empty($fillableData)) {
            return false;
        }
        
        // Hash password if it's being updated
        if (isset($fillableData['password']) && !empty($fillableData['password'])) {
            $fillableData['password'] = password_hash($fillableData['password'], PASSWORD_DEFAULT);
        }
        
        $setClause = [];
        foreach ($fillableData as $key => $value) {
            $setClause[] = "$key = :$key";
        }
        
        $query = "UPDATE users SET " . implode(', ', $setClause) . " WHERE id = :id";
        $stmt = db()->prepare($query);
        
        // Bind parameters
        foreach ($fillableData as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':id', $this->id);
        
        return $stmt->execute();
    }

    /**
     * Save the user (create or update)
     */
    public function save()
    {
        if (isset($this->id)) {
            return $this->update();
        } else {
            $data = get_object_vars($this);
            $newUser = static::create($data);
            if ($newUser) {
                $this->id = $newUser->id;
                return true;
            }
            return false;
        }
    }

    /**
     * Delete the user
     */
    public function delete()
    {
        if (!isset($this->id)) {
            return false;
        }
        
        $stmt = db()->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }

    /**
     * Get all users with pagination
     */
    public static function paginate($perPage = 15, $page = 1)
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = db()->prepare("SELECT * FROM users LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $users = [];
        while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = static::createInstance($userData);
        }
        
        // Get total count for pagination info
        $countStmt = db()->prepare("SELECT COUNT(*) as total FROM users");
        $countStmt->execute();
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        return [
            'data' => $users,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    /**
     * Accessors for appended attributes
     */

    /**
     * Get full name attribute
     */
    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get images attribute
     */
    public function getImagesAttribute()
    {
        // Simple media implementation - you can enhance this later
        if (!empty($this->avatar)) {
            return asset($this->avatar);
        }
        return asset('images/user.png');
    }

    /**
     * Get myrole attribute
     */
    public function getMyroleAttribute()
    {
        // Simple role implementation
        return $this->roles ? json_decode($this->roles, true) : [];
    }

    /**
     * Get my status attribute with HTML
     */
    public function getMyStatusAttribute()
    {
        if ($this->status == Status::ACTIVE) {
            return '<span class="text-green-600 bg-green-100 db-table-badge">' . $this->getStatusText() . '</span>';
        } else {
            return '<span class="text-red-600 bg-red-100 db-table-badge">' . $this->getStatusText() . '</span>';
        }
    }

    /**
     * Get country code with phone
     */
    public function getCountryCodeWithPhoneAttribute()
    {
        return '+' . $this->country_code . $this->phone;
    }

    /**
     * Get status text
     */
    protected function getStatusText()
    {
        $statuses = [
            Status::ACTIVE => 'Active',
            Status::INACTIVE => 'Inactive',
            // Status::PENDING => 'Pending'
        ];
        
        return $statuses[$this->status] ?? 'Unknown';
    }

    /**
     * Route notification for Twilio
     */
    public function routeNotificationForTwilio()
    {
        return '+' . $this->country_code . $this->phone;
    }

    /**
     * Route notification for FCM
     */
    public function routeNotificationForFcm($notification)
    {
        return $this->device_token;
    }

    /**
     * Relationship with Employee (simplified)
     */
    public function employee()
    {
        // Simple one-to-one relationship implementation
        return Employee::where('user_id', $this->id)->first();
    }

    /**
     * Get role relationship (simplified)
     */
    public function getrole()
    {
        // Simple role implementation
        if (!empty($this->myrole)) {
            return Role::find($this->myrole);
        }
        return null;
    }

  

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->status == Status::ACTIVE;
    }

    /**
     * Magic getter for properties and accessors
     */
    public function __get($property)
    {
        // Check if it's an accessor
        $accessor = 'get' . str_replace('_', '', ucwords($property, '_')) . 'Attribute';
        if (method_exists($this, $accessor)) {
            return $this->$accessor();
        }
        
        // Check if it's a regular property
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        
        // Check if it's in appended attributes
        if (in_array($property, $this->appends)) {
            $accessor = 'get' . str_replace('_', '', ucwords($property, '_')) . 'Attribute';
            if (method_exists($this, $accessor)) {
                return $this->$accessor();
            }
        }
        
        return null;
    }

    /**
     * Magic setter for properties
     */
    public function __set($property, $value)
    {
        if (in_array($property, $this->fillable) || in_array($property, ['id', 'email_verified_at', 'remember_token'])) {
            $this->$property = $value;
        }
    }

    /**
     * Convert the model instance to an array.
     */
    public function toArray()
    {
        $data = [];
        
        // Add regular attributes
        foreach (get_object_vars($this) as $key => $value) {
            if (!in_array($key, $this->hidden)) {
                $data[$key] = $value;
            }
        }
        
        // Add appended attributes
        foreach ($this->appends as $attribute) {
            $data[$attribute] = $this->$attribute;
        }
        
        return $data;
    }
}