<?php

declare(strict_types=1);

namespace tests\Phpml\Math;

use Phpml\Math\Matrix;
use PHPUnit\Framework\TestCase;

class MatrixTest extends TestCase
{
    /**
     * @expectedException \Phpml\Exception\InvalidArgumentException
     */
    public function testThrowExceptionOnInvalidMatrixSupplied()
    {
        new Matrix([[1, 2], [3]]);
    }

    public function testCreateMatrixFromFlatArray()
    {
        $flatArray = [1, 2, 3, 4];
        $matrix = Matrix::fromFlatArray($flatArray);

        $this->assertInstanceOf(Matrix::class, $matrix);
        $this->assertEquals([[1], [2], [3], [4]], $matrix->toArray());
        $this->assertEquals(4, $matrix->getRows());
        $this->assertEquals(1, $matrix->getColumns());
        $this->assertEquals($flatArray, $matrix->getColumnValues(0));
    }

    /**
     * @expectedException \Phpml\Exception\MatrixException
     */
    public function testThrowExceptionOnInvalidColumnNumber()
    {
        $matrix = new Matrix([[1, 2, 3], [4, 5, 6]]);
        $matrix->getColumnValues(4);
    }

    /**
     * @expectedException \Phpml\Exception\MatrixException
     */
    public function testThrowExceptionOnGetDeterminantIfArrayIsNotSquare()
    {
        $matrix = new Matrix([[1, 2, 3], [4, 5, 6]]);
        $matrix->getDeterminant();
    }

    public function testGetMatrixDeterminant()
    {
        //http://matrix.reshish.com/determinant.php
        $matrix = new Matrix([
            [3, 3, 3],
            [4, 2, 1],
            [5, 6, 7],
        ]);
        $this->assertEquals(-3, $matrix->getDeterminant());

        $matrix = new Matrix([
            [1, 2, 3, 3, 2, 1],
            [1 / 2, 5, 6, 7, 1, 1],
            [3 / 2, 7 / 2, 2, 0, 6, 8],
            [1, 8, 10, 1, 2, 2],
            [1 / 4, 4, 1, 0, 2, 3 / 7],
            [1, 8, 7, 5, 4, 4 / 5],
        ]);
        $this->assertEquals(1116.5035, $matrix->getDeterminant(), '', $delta = 0.0001);
    }

    public function testMatrixTranspose()
    {
        $matrix = new Matrix([
            [3, 3, 3],
            [4, 2, 1],
            [5, 6, 7],
        ]);

        $transposedMatrix = [
            [3, 4, 5],
            [3, 2, 6],
            [3, 1, 7],
        ];

        $this->assertEquals($transposedMatrix, $matrix->transpose()->toArray());
    }

    /**
     * @expectedException \Phpml\Exception\InvalidArgumentException
     */
    public function testThrowExceptionOnMultiplyWhenInconsistentMatrixSupplied()
    {
        $matrix1 = new Matrix([[1, 2, 3], [4, 5, 6]]);
        $matrix2 = new Matrix([[3, 2, 1], [6, 5, 4]]);

        $matrix1->multiply($matrix2);
    }

    public function testMatrixMultiplyByMatrix()
    {
        $matrix1 = new Matrix([
            [1, 2, 3],
            [4, 5, 6],
        ]);

        $matrix2 = new Matrix([
            [7, 8],
            [9, 10],
            [11, 12],
        ]);

        $product = [
            [58, 64],
            [139, 154],
        ];

        $this->assertEquals($product, $matrix1->multiply($matrix2)->toArray());
    }

    public function testDivideByScalar()
    {
        $matrix = new Matrix([
            [4, 6, 8],
            [2, 10, 20],
        ]);

        $quotient = [
            [2, 3, 4],
            [1, 5, 10],
        ];

        $this->assertEquals($quotient, $matrix->divideByScalar(2)->toArray());
    }

    /**
     * @expectedException \Phpml\Exception\MatrixException
     */
    public function testThrowExceptionWhenInverseIfArrayIsNotSquare()
    {
        $matrix = new Matrix([[1, 2, 3], [4, 5, 6]]);
        $matrix->inverse();
    }

    /**
     * @expectedException \Phpml\Exception\MatrixException
     */
    public function testThrowExceptionWhenInverseIfMatrixIsSingular()
    {
        $matrix = new Matrix([
          [0, 0, 0],
          [0, 0, 0],
          [0, 0, 0],
       ]);

        $matrix->inverse();
    }

    public function testInverseMatrix()
    {
        //http://ncalculators.com/matrix/inverse-matrix.htm
        $matrix = new Matrix([
            [3, 4, 2],
            [4, 5, 5],
            [1, 1, 1],
        ]);

        $inverseMatrix = [
            [0, -1, 5],
            [1 / 2, 1 / 2, -7 / 2],
            [-1 / 2, 1 / 2, -1 / 2],
        ];

        $this->assertEquals($inverseMatrix, $matrix->inverse()->toArray(), '', $delta = 0.0001);
    }

    public function testCrossOutMatrix()
    {
        $matrix = new Matrix([
            [3, 4, 2],
            [4, 5, 5],
            [1, 1, 1],
        ]);

        $crossOuted = [
            [3, 2],
            [1, 1],
        ];

        $this->assertEquals($crossOuted, $matrix->crossOut(1, 1)->toArray());
    }

    public function testToScalar()
    {
        $matrix = new Matrix([[1, 2, 3], [3, 2, 3]]);

        $this->assertEquals($matrix->toScalar(), 1);
    }

    public function testMultiplyByScalar()
    {
        $matrix = new Matrix([
            [4, 6, 8],
            [2, 10, 20],
        ]);

        $result = [
            [-8, -12, -16],
            [-4, -20, -40],
        ];

        $this->assertEquals($result, $matrix->multiplyByScalar(-2)->toArray());
    }

    public function testAdd()
    {
        $array1 = [1, 1, 1];
        $array2 = [2, 2, 2];
        $result = [3, 3, 3];

        $m1 = new Matrix($array1);
        $m2 = new Matrix($array2);

        $this->assertEquals($result, $m1->add($m2)->toArray()[0]);
    }

    public function testSubtract()
    {
        $array1 = [1, 1, 1];
        $array2 = [2, 2, 2];
        $result = [-1, -1, -1];

        $m1 = new Matrix($array1);
        $m2 = new Matrix($array2);

        $this->assertEquals($result, $m1->subtract($m2)->toArray()[0]);
    }

    public function testTransposeArray()
    {
        $array = [
            [1, 1, 1],
            [2, 2, 2]
        ];
        $transposed = [
            [1, 2],
            [1, 2],
            [1, 2]
        ];

        $this->assertEquals($transposed, Matrix::transposeArray($array));
    }

    public function testDot()
    {
        $vect1 = [2, 2, 2];
        $vect2 = [3, 3, 3];
        $dot = [18];

        $this->assertEquals($dot, Matrix::dot($vect1, $vect2));

        $matrix1 = [[1, 1], [2, 2]];
        $matrix2 = [[3, 3], [3, 3], [3, 3]];
        $dot = [6, 12];
        
        $this->assertEquals($dot, Matrix::dot($matrix2, $matrix1));
    }
}
