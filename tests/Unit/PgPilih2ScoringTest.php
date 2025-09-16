<?php

namespace Tests\Unit;

use App\Http\Controllers\TryoutController;
use App\Models\Soal;
use App\Models\OpsiSoal;
use PHPUnit\Framework\TestCase;

class PgPilih2ScoringTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new TryoutController();
    }

    /**
     * Test scoring logic for pg_pilih_2 questions
     */
    public function test_pg_pilih_2_scoring_logic()
    {
        // Create a mock soal with pg_pilih_2 type
        $soal = new Soal();
        $soal->tipe = 'pg_pilih_2';
        
        // Create mock opsi with correct answers (A and C)
        $opsiA = new OpsiSoal();
        $opsiA->opsi = 'A';
        $opsiA->bobot = 1; // Correct answer
        
        $opsiB = new OpsiSoal();
        $opsiB->opsi = 'B';
        $opsiB->bobot = 0; // Incorrect answer
        
        $opsiC = new OpsiSoal();
        $opsiC->opsi = 'C';
        $opsiC->bobot = 1; // Correct answer
        
        $opsiD = new OpsiSoal();
        $opsiD->opsi = 'D';
        $opsiD->bobot = 0; // Incorrect answer

        // Mock the opsi relationship
        $soal->shouldReceive('opsi')->andReturnSelf();
        $soal->shouldReceive('where')->with('opsi', 'A')->andReturnSelf();
        $soal->shouldReceive('first')->andReturn($opsiA);
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('calculateScore');
        $method->setAccessible(true);

        // Test Case 1: Both correct answers (A, C) - should return 1
        $jawaban1 = ['A', 'C'];
        $score1 = $method->invoke($this->controller, $soal, $jawaban1);
        $this->assertEquals(1, $score1, 'Both correct answers should return score 1');

        // Test Case 2: One correct, one incorrect (A, B) - should return 0
        $jawaban2 = ['A', 'B'];
        $score2 = $method->invoke($this->controller, $soal, $jawaban2);
        $this->assertEquals(0, $score2, 'One correct one incorrect should return score 0');

        // Test Case 3: Both incorrect (B, D) - should return 0
        $jawaban3 = ['B', 'D'];
        $score3 = $method->invoke($this->controller, $soal, $jawaban3);
        $this->assertEquals(0, $score3, 'Both incorrect answers should return score 0');

        // Test Case 4: Less than 2 answers - should return 0
        $jawaban4 = ['A'];
        $score4 = $method->invoke($this->controller, $soal, $jawaban4);
        $this->assertEquals(0, $score4, 'Less than 2 answers should return score 0');

        // Test Case 5: More than 2 answers - should return 0
        $jawaban5 = ['A', 'B', 'C'];
        $score5 = $method->invoke($this->controller, $soal, $jawaban5);
        $this->assertEquals(0, $score5, 'More than 2 answers should return score 0');
    }
}
