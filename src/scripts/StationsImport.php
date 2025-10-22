<?php
  namespace Script;

  use Model\Station;

  class StationsImport {
    public static function run(): void {
      $file = __DIR__ . '/../dataset/gares-de-voyageurs.csv';

      if (!file_exists($file)) {
        echo "❌ File not found: {$file}\n";
        return;
      }

      if (($handle = fopen($file, 'r')) !== false) {
        $count = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
          $name = trim($row[0]);
          $geo = trim($row[3]);
          $postal_code = trim($row[4]);

          $coords = preg_split('/\s+/', $geo);
          $latitude = isset($coords[0]) ? floatval($coords[0]) : null;
          $longitude = isset($coords[1]) ? floatval($coords[1]) : null;

          if (!$latitude || !$longitude) {
            echo "⏩ Skipped {$name} because lack of data\n";
            continue;
          };

          Station::firstOrCreate([
            'name' => $name,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'postal_code' => $postal_code
          ]);
          
          $count++;
          echo "Import {$name}\n";
        }

        fclose($handle);
        echo "✅ Import done ({$count} rows)";

      } else {
        echo "❌ Impossible import: cannot open CSV file";
      }
    }
  }