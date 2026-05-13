<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

class ERDController extends Controller
{
    public function index()
    {
        $tables = [
            [
                'name' => 'users',
                'columns' => [
                    'id',
                    'name',
                    'email',
                    'password'
                ]
            ],
            [
                'name' => 'posts',
                'columns' => [
                    'id',
                    'user_id',
                    'title',
                    'description'
                ]
            ],
            [
                'name' => 'comments',
                'columns' => [
                    'id',
                    'post_id',
                    'comment'
                ]
            ],
        ];

        return view('erd', compact('tables'));
    }

    public function exportPDF()
    {
        $tables = [
            [
                'name' => 'users',
                'columns' => [
                    'id',
                    'name',
                    'email',
                    'password'
                ]
            ],
            [
                'name' => 'posts',
                'columns' => [
                    'id',
                    'user_id',
                    'title',
                    'description'
                ]
            ],
            [
                'name' => 'comments',
                'columns' => [
                    'id',
                    'post_id',
                    'comment'
                ]
            ],
        ];

        $pdf = Pdf::loadView('pdf.erd-pdf', compact('tables'));

        return $pdf->download('ERD-Diagram.pdf');
    }
}