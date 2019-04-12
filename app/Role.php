<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class Role extends Model
    {
        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'name', 'description'
        ];

        /* Eloquent many to many function */
        public function users() {
            return $this->belongsToMany('App\User', "user_role")->withPivot('id')->withTimestamps();
        }

        /* Eloquent many to many function */
        public function permissions() {
            return $this->belongsToMany('App\Permission', 'role_permission')->withPivot('id')->withTimestamps();
        }
        
        public function addUser($userId) {
            $this->users()->attach($userId);
        }

        public function removeUser($userId) {
            $this->users()->detach($userId);
        }
        
        public function addPermissions($permissionIds) {
            $this->permissions()->attach($permissionIds);
        }

        public function resetPermission() {
            $this->permissions()->detach();
        }
    }
