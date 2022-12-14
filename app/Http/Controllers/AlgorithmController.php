<?php

namespace App\Http\Controllers;

use App\Models\AssociationRule;
use App\Models\Product;
use App\Models\Rule;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Phpml\Association\Apriori;

class AlgorithmController extends Controller
{
    /**
     * > This function gets all the products from the database and stores them in the `product` variable
     */
    public function getProduct()
    {
        $this->product = Product::get()->toArray();
    }
    /**
     * It takes an array of product codes and returns an array of product names
     *
     * @param jsonArray The array of product codes that you want to change.
     *
     * @return array product name of the product code that is passed in.
     */
    public function changeCodeProducts($jsonArray)
    {
        $keyList = array_column($this->product, 'product_code');
        $products = [];
        foreach ($jsonArray as $value) {
            $key = array_search($value, $keyList);
            if ($key !== false) {
                $products[] = $this->product[$key]['product_name'];
            }
        }
        return $products;
    }
    /**
     * It returns an array with two keys, `resource` and `total`. The `resource` key contains the paginated
     * results of the `AssociationRule::cursorPaginate()` function, and the `total` key contains the total
     * number of results
     *
     * @return array with the key "resource" and the value of the cursorPaginate method of the
     * AssociationRule model.
     */
    public function getData(Request $request)
    {
        $rule = Rule::find(1);
        $dataAssociation = AssociationRule::where('rule_confidence', '=', $rule->confidence)
            ->where('rule_support', '=', $rule->support)->paginate();

        $this->getProduct();
        foreach ($dataAssociation as &$datum) {
            $datum['consequentConcat'] = implode(',', $this->changeCodeProducts(json_decode($datum->consequent)));
            $datum['antecedentConcat'] = implode(',', $this->changeCodeProducts(json_decode($datum->antecedent)));
            $datum['description'] =
                [
                    ['word' => "Jika membeli "],
                    ['word' => $datum['antecedentConcat'], 'font' => 'bold'],
                    ['word' => " maka akan membeli "],
                    ['word' => $datum['consequentConcat'], 'font' => 'bold'],
                    ['word' => " dengan nilai support "],
                    ['word' => round($datum['support'] * 100, 2) . "%", 'font' => 'bold'],
                    ['word' => " dan nilai confidence "],
                    ['word' => round($datum['confidence'] * 100, 2) . "%", 'font' => 'bold']
                ];
        }
        return [
            "resource" => $dataAssociation,
            "total" => AssociationRule::where('rule_confidence', '=', $rule->confidence)
                ->where('rule_support', '=', $rule->support)
                ->count()
        ];
    }
    /**
     * It transforms the data from the database into a format that is more suitable for the Apriori
     * algorithm
     *
     * @param data The data to be transformed.
     *
     * @return [
     *         'sample' => [
     *             [
     *                 'INV-001',
     *                 'PRD-001',
     *                 'PRD-002',
     *                 'PRD-003',
     *                 'PRD-004',
     *                 'PRD-005',
     *                 'PRD-006',
     *                 'PRD-007',
     */
    public function transform($data)
    {
        $newData = [];
        $dataProducts = [];
        foreach ($data as $datum) {
            $dataProducts[$datum->product->product_code] = $datum->product->product_name;
            $newData[$datum->transactionList->no_invoice][] = $datum->product->product_code;
        }
        return [
            'sample' => array_values($newData),
            'dataProducts' => $dataProducts
        ];
    }
    /**
     * > The function will get the data from the database, then transform the data into a format that can
     * be used by the Apriori algorithm, then train the algorithm, and finally save the result to the
     * database
     *
     * @return Array The rules that have been generated.
     */
    public function index(Request $request)
    {
        $rule = Rule::find(1);
        $transaction = Transaction::with('product', 'transactionList')->get();
        if ($rule && $transaction) {
            ['sample' => $samples, 'dataProducts' => $dataProducts] = $this->transform($transaction);
            $labels  = [];

            $associator = new Apriori($rule->support, $rule->confidence);
            $associator->train($samples, $labels);
            $data = $associator->getRules();
            AssociationRule::truncate();
            foreach ($data as $datum) {
                AssociationRule::create(
                    [
                        'consequent' => json_encode($datum['consequent']),
                        'antecedent' => json_encode($datum['antecedent']),
                        'consequentSet' => count($datum['consequent']),
                        'antecedentSet' => count($datum['antecedent']),
                        'confidence' => $datum['confidence'],
                        'support' => $datum['support'],
                        'rule_confidence' => $rule->confidence,
                        'rule_support' => $rule->support,
                        'month' => 0,
                        'year' => 0,
                    ]
                );
            }
            // $this->autoImportProduct($dataProducts);
            return $associator->getRules();
        }
    }
    public function autoImportProduct($dataProducts)
    {
        foreach ($dataProducts as $productCode => $productName) {
            if (!in_array($productCode, array_column($this->product, 'product_code'))) {
                Product::create([
                    'product_name' => $productName,
                    'product_code' => $productCode,
                    'price' => 0,
                    'unit' => 0,
                ]);
            }
        }
    }
}
