<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Drosophila;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class SeededRandom {
    private $_seed;

    public function __construct($seed) {
      $this->_seed = $seed;
    }

    public function random() {
      $this->_seed = ($this->_seed * 1664525 + 1013904223) % 4294967296;
      return $this->_seed / 4294967296;
    }
  }

  function getRandomElement($array, $seededRandom) {
    $randomIndex = $seededRandom->random();
    return $array[floor($randomIndex * count($array))];
  }

  function generateOffspring($mother, $father, $seededRandom) {
    $offspringSexChromosome = getRandomElement(["X", "Y"], $seededRandom);
    $offspringSex = $offspringSexChromosome === "X" ? "Female" : "Male";

    return [
      "sex" => $offspringSex,
      "trait1" => [getRandomElement($mother["trait1"], $seededRandom), getRandomElement($father["trait1"], $seededRandom)],
      "trait2" => [getRandomElement($mother["trait2"], $seededRandom), getRandomElement($father["trait2"], $seededRandom)],
      "trait3" => [getRandomElement($mother["trait3"], $seededRandom), getRandomElement($father["trait3"], $seededRandom)],
    ];
  }

class DrosophilaController extends Controller
{
    public function index()
    {
        $drosophila = Drosophila::all();
        return response()->json([
            'message' => 'success',
            'data' => $drosophila
        ], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $file = $request->file('file');
        $csvData = file_get_contents($file);
        $rows = array_map('str_getcsv', explode("\n", $csvData));
        $main = array();
        $mixed = array();
        $show = array();
        $type = array();
        foreach ($rows as $row) {
            $row = array_filter($row);
            if (!empty($row)) {
                $main[] = trim($row[0]);
                $mixed[] = trim($row[1]);
                $show[] = trim($row[2]);
                $type[] = trim($row[3]);
            }
        }
        foreach ($main as $key => $value) {
            $drosophila = new Drosophila();
            // trim data
            $drosophila->main = $value;
            $drosophila->mixed = $mixed[$key];
            $drosophila->show = $show[$key];
            $drosophila->type = $type[$key];
            $drosophila->save();
        }
        $data = Drosophila::orderBy('id', 'desc')->take(10)->get();
        return response()->json([
            'message' => 'success',
            'data' => null
        ], Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $drosophila = Drosophila::find($id);
        $drosophila->delete();
        return response()->json([
            'message' => 'success',
            'data' => null
        ], Response::HTTP_OK);
    }

    public function mixed(Request $request)
    {
        $maleParent = $request->input('maleParent');
        $femaleParent = $request->input('femaleParent');
        if (!$maleParent || !$femaleParent) {
            return response()->json([
                'message' => 'Missing parent data',
                'data' => null
            ], Response::HTTP_BAD_REQUEST);
        }
        $random_number = mt_rand(1000000, 9999999);
        $seededRandom = new SeededRandom($random_number);
        $numOffspring = 100;
        $offspringList = [];
        for ($i = 0; $i < $numOffspring; $i++) {
          array_push($offspringList, generateOffspring($femaleParent, $maleParent, $seededRandom));
        }
        return response()->json([
            'message' => 'success',
            'data' => [
                'sdd' => $random_number,
                'maleParent' => $maleParent,
                'femaleParent' => $femaleParent,
            ]
        ], Response::HTTP_OK);
    }
}
