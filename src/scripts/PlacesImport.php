<?php
  namespace Script;

  use Model\Place;

  class PlacesImport {
    public static function run(): void {
      $file = __DIR__ . '/../dataset/basilic-dataculture-2025-09-05.csv';
      $count = 0;

      $ignoredTypes = [
        "Établissement d'enseignement supérieur", 
        "Papeterie et maisons de la presse",
        "Service d'archives"
      ];

      if (!file_exists($file)) {
        echo "❌ File not found: {$file}\n";
        return;
      }

      if (($handle = fopen($file, 'r')) !== false) {
        $count = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
          $name = trim($row[1] ?? '');
          $type = trim($row[9] ?? '');
          $label = trim($row[10] ?? '');
          $postal_code = trim($row[4] ?? '');
          $city = trim($row[5] ?? '');
          $address = trim($row[2] ?? '');
          $latitude = isset($row[45]) ? floatval($row[45]) : null;
          $longitude = isset($row[46]) ? floatval($row[46]) : null;

          if (!$name || !$latitude || !$longitude) {
            echo "⏩ Skipped {$name} because lack of data\n";
            continue;
          }

          if (in_array($type, $ignoredTypes)) {
            echo "⏩ Skipped {$name} because of type ({$type})" . PHP_EOL;
            continue;
          }

          Place::firstOrCreate(
            [
              'name' => $name, 
              'type' => $type,
              'label' => $label,
              'postal_code' => $postal_code,
              'city' => $city,
              'address' => $address,
              'latitude' => $latitude,
              'longitude' => $longitude
            ]
          );

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
