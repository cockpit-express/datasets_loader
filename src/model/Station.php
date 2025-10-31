<?php
  namespace Model;

  use Illuminate\Database\Eloquent\Model;

  class Station extends Model {
    protected $table = 'stations';
    public $timestamps = false;
    protected $fillable = [
      'name',      // string
      'latitude',  // float
      'longitude', // float
      'postal_code'  // int
    ];
  }