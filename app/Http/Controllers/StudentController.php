<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StudentController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL') . '/students';
    }

    public function index()
    {
        $response = Http::get($this->apiUrl);
        $students = $response->json();

        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        Http::post($this->apiUrl, $request->all());

        return redirect()->route('students.index')->with('success', 'Student successfully created.');
    }

    public function edit($id)
    {
        $response = Http::get("{$this->apiUrl}/{$id}");
        $student = $response->json();

        return view('students.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        Http::put("{$this->apiUrl}/{$id}", $request->all());

        return redirect()->route('students.index')->with('success', 'Student successfully updated.');
    }

    public function destroy($id)
    {
        $response = Http::delete("{$this->apiUrl}/{$id}");

        if ($response->successful()) {
            return redirect()->route('students.index')->with('success', 'Grade successfully deleted');
        }

        return redirect()->route('students.index')->with('success', 'Student successfully deleted.');
    }

    public function expedient($studentId)
    {
        $gradesResponse = Http::get(env('API_URL') . '/grades/student/' . $studentId);
    
        if ($gradesResponse->successful()) {
            $grades = $gradesResponse->json();
    
            foreach ($grades as &$grade) {
                $subjectId = $grade['subject_id'];
    
                $subjectResponse = Http::get(env('API_URL') . '/subjects/' . $subjectId);
    
                if ($subjectResponse->successful()) {
                    $subject = $subjectResponse->json();
                    $grade['subject_name'] = $subject['name'] ?? 'Unknown Subject';
                    $grade['course_level'] = $subject['course_level'] ?? 'Unknown Level';
                } else {
                    $grade['subject_name'] = 'Unknown Subject';
                    $grade['course_level'] = 'Unknown Level';
                }
            }
        } else {
            $grades = [];
        }
    
        $studentResponse = Http::get(env('API_URL') . '/students/' . $studentId);
    
        if ($studentResponse->successful()) {
            $student = $studentResponse->json();
        } else {
            $student = null;
        }
    
        return view('students.expedient', compact('grades', 'student'));
    }
    
}
