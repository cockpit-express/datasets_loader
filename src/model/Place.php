<?php
  namespace Model;

  use Illuminate\Database\Eloquent\Model;

  class Place extends Model {
    protected $table = 'places';
    public $timestamps = false;

    protected $fillable = [
      'name',        // string
      'type',        // string
      'address',     // string
      'city',        // string
      'label',       // string
      'postal_code', // int
      'latitude',    // float
      'longitude'    // float
    ];
  }