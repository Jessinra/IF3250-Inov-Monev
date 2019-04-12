<?php

    namespace App;

    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Laravel\Passport\HasApiTokens;

    class User extends Authenticatable
    {
        use Notifiable, HasApiTokens;

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'name', 'username', 'email', 'password',
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            'password', 'remember_token',
        ];

        /**
         * The attributes that should be cast to native types.
         *
         * @var array
         */
        protected $casts = [
            'email_verified_at' => 'datetime',
        ];

        /* Eloquent many to many function */
        public function roles() {
            return $this->belongsToMany('App\Role', "user_role")->withPivot('id')->withTimestamps();
        }

        /* Eloquent many to many function */
        public function groups() {
            return $this->belongsToMany('App\Group', "user_group")->withPivot('id')->withTimestamps();
        }

        public function projects() {
            return $this->hasMany('App\Project', "user_project")->withPivot('id')->withTimestamps();
        }

        public function notes() {
            return $this->hasMany('App\Note', "user_note")->withPivot('id')->withTimestamps();
        }

        public function addRole($roleId) {
            $this->roles()->attach($roleId);
        }

        public function removeRole($roleId) {
            $this->roles()->detach($roleId);
        }

        public function addGroup($groupId) {
            $this->groups()->attach($groupId);
        }

        public function removeGroup($groupId) {
            $this->groups()->detach($groupId);
        }

        public function addProject($projectId) {
            $this->projects()->attach($projectId);
        }

        public function removeProject($projectId) {
            $this->projects()->detach($projectId);
        }

        public function addNote($noteId) {
            $this->notes()->attach($noteId);
        }

        public function removeNote($noteId) {
            $this->notes()->detach($noteId);
        }
    }
