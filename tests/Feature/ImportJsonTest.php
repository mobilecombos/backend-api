<?php

namespace Tests\Feature;

use App\Models\CapabilitySet;
use App\Models\Combo;
use App\Models\Device;
use App\Models\LteComponent;
use App\Models\Mimo;
use App\Models\Modulation;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ImportJsonTest extends TestCase
{
    use RefreshDatabase;
    use ArraySubsetAsserts;

    protected $seed = true;

    protected static $auth = [
        'x-auth-token' => 'admin',
    ];

    protected static $lte_ca_data = [
        'lteca' => [
            [
                'components' => [
                    [
                        'band'      => 1,
                        'bwClassDl' => 'A',
                        'bwClassUl' => 'A',
                        'mimoDl'    => [
                            'type'  => 'single',
                            'value' => 4,
                        ],
                        'mimoUl' => [
                            'type'  => 'single',
                            'value' => 1,
                        ],
                        'modulationDl' => [
                            'type'  => 'single',
                            'value' => 'qam256',
                        ],
                        'modulationUl' => [
                            'type'  => 'single',
                            'value' => 'qam64',
                        ],
                    ],
                ],
                'bcs' => [
                    'type'  => 'single',
                    'value' => 0,
                ],
            ],
            [
                'components' => [
                    [
                        'band'      => 3,
                        'bwClassDl' => 'A',
                        'bwClassUl' => 'A',
                        'mimoDl'    => [
                            'type'  => 'single',
                            'value' => 4,
                        ],
                        'mimoUl' => [
                            'type'  => 'single',
                            'value' => 1,
                        ],
                        'modulationDl' => [
                            'type'  => 'single',
                            'value' => 'qam256',
                        ],
                        'modulationUl' => [
                            'type'  => 'single',
                            'value' => 'qam64',
                        ],
                    ],
                ],
            ],
            [
                'components' => [
                    [
                        'band'      => 7,
                        'bwClassDl' => 'C',
                        'bwClassUl' => 'C',
                        'mimoDl'    => [
                            'type'  => 'mixed',
                            'value' => [2, 4],
                        ],
                        'mimoUl' => [
                            'type'  => 'mixed',
                            'value' => [1, 2],
                        ],
                        'modulationDl' => [
                            'type'  => 'mixed',
                            'value' => ['qam256', 'qam1024'],
                        ],
                        'modulationUl' => [
                            'type'  => 'mixed',
                            'value' => ['qam64', 'qam256'],
                        ],
                    ],
                ],
                'bcs' => [
                    'type'  => 'multi',
                    'value' => [1, 2, 3],
                ],
            ],
            [
                'components' => [
                    [
                        'band'      => 1,
                        'bwClassDl' => 'A',
                        'bwClassUl' => 'A',
                        'mimoDl'    => [
                            'type'  => 'single',
                            'value' => 4,
                        ],
                        'mimoUl' => [
                            'type'  => 'single',
                            'value' => 1,
                        ],
                        'modulationDl' => [
                            'type'  => 'single',
                            'value' => 'qam256',
                        ],
                        'modulationUl' => [
                            'type'  => 'single',
                            'value' => 'qam64',
                        ],
                    ],
                ],
                'bcs' => [
                    'type'  => 'all',
                ],
            ],
            [
                'components' => [
                    [
                        'band'      => 1,
                        'bwClassDl' => 'A',
                        'bwClassUl' => 'A',
                        'mimoDl'    => [
                            'type'  => 'single',
                            'value' => 4,
                        ],
                        'mimoUl' => [
                            'type'  => 'single',
                            'value' => 2,
                        ],
                        'modulationDl' => [
                            'type'  => 'single',
                            'value' => 'qam256',
                        ],
                        'modulationUl' => [
                            'type'  => 'single',
                            'value' => 'qam64',
                        ],
                    ],
                    [
                        'band'      => 3,
                        'mimoDl'    => [
                            'type'  => 'empty',
                        ],
                        'mimoUl' => [
                            'type'  => 'empty',
                        ],
                    ],
                    [
                        'band'         => 32,
                        'bwClassDl'    => 'A',
                        'modulationDl' => [
                            'type'  => 'empty',
                        ],
                    ],
                ],
                'bcs' => [
                    'type'  => 'empty',
                ],
            ],
        ],
    ];

    /**
     * Cannot parse a log without any data.
     */
    public function test_cannot_parse_log_without_data(): void
    {
        $response = $this->post('/v1/actions/import-json', [], ImportJsonTest::$auth);

        $response->assertStatus(422);
        $response->assertJson(['errors' => ['jsonData' => [], 'deviceId' => [], 'capabilitySetId' => []]]);
        $this->assertStringContainsString('field is required', $response->getContent() ?: '');
    }

    /**
     * Cannot parse a log without a valid device ID.
     */
    public function test_cannot_parse_log_with_invalid_device_id(): void
    {
        $response = $this->post('/v1/actions/import-json', ['jsonData' => 'test', 'deviceId' => Device::first()->id, 'capabilitySetId' => 99999999], ImportJsonTest::$auth);

        $response->assertStatus(422);
        $response->assertJson(['errors' => ['capabilitySetId' => []]]);
        $this->assertStringContainsString('capability set id is invalid', $response->getContent() ?: '');
    }

    /**
     * Cannot parse a log without a valid capability set ID.
     */
    public function test_cannot_parse_log_with_invalid_capability_set_id(): void
    {
        $response = $this->post('/v1/actions/import-json', ['jsonData' => 'test', 'deviceId' => 99999999, 'capabilitySetId' => CapabilitySet::first()->id], ImportJsonTest::$auth);

        $response->assertStatus(422);
        $response->assertJson(['errors' => ['deviceId' => []]]);
        $this->assertStringContainsString('device id is invalid', $response->getContent() ?: '');
    }

    /**
     * Can import a valid lte ca data JSON output.
     */
    public function test_imports_lte_ca_data(): void
    {
        /** @var CapabilitySet */
        $testingCapabilitySet = CapabilitySet::first();
        /** @var Device */
        $testingDevice = $testingCapabilitySet->device;

        $response = $this->post('/v1/actions/import-json', ['jsonData' => json_encode(ImportJsonTest::$lte_ca_data), 'deviceId' => $testingDevice->id, 'capabilitySetId' => $testingCapabilitySet->id], ImportJsonTest::$auth);

        $response->assertStatus(200);
        $this->assertSame('null', $response->getContent());

        $testingCapabilitySet->refresh();
        $combos = $testingCapabilitySet->combos;

        $this->assertSame(5, $combos->count());

        // ##############################
        // Combo 1
        // ##############################

        /** @var Combo */
        $combo = $combos->get(0);

        $this->assertArraySubset([
            'combo_string'      => '1A4A',
            'capability_set_id' => $testingCapabilitySet->id,
        ], $combo->getAttributes());
        $this->assertSame([0], $combo->bandwidth_combination_set_eutra);

        $comboComponents = $combo->lteComponents;
        $this->assertSame(1, $comboComponents->count());

        $this->assertSame(Arr::except($comboComponents->first()->getAttributes(), 'id'), [
            'band'            => 1,
            'dl_class'        => 'A',
            'ul_class'        => 'A',
            'component_index' => 0,
        ]);

        /** @var LteComponent */
        $cc = $comboComponents->first();

        $this->assertSame($cc->dl_mimos()->count(), 1);
        $this->assertSame($cc->ul_mimos()->count(), 1);

        $this->assertSame(4, $cc->dl_mimos()->first()->mimo);
        $this->assertSame(1, $cc->ul_mimos()->first()->mimo);

        // ##############################
        // Combo 2
        // ##############################

        /** @var Combo */
        $combo = $combos->get(1);

        $this->assertArraySubset([
            'combo_string'      => '3A4A',
            'capability_set_id' => $testingCapabilitySet->id,
        ], $combo->getAttributes());
        $this->assertSame([], $combo->bandwidth_combination_set_eutra);

        $comboComponents = $combo->lteComponents;
        $this->assertSame(1, $comboComponents->count());

        $this->assertSame(Arr::except($comboComponents->first()->getAttributes(), 'id'), [
            'band'            => 3,
            'dl_class'        => 'A',
            'ul_class'        => 'A',
            'component_index' => 0,
        ]);

        // ##############################
        // Combo 3
        // ##############################

        /** @var Combo */
        $combo = $combos->get(2);

        $this->assertArraySubset([
            'combo_string'      => '7C4C2',
            'capability_set_id' => $testingCapabilitySet->id,
        ], $combo->getAttributes());
        $this->assertSame([1, 2, 3], $combo->bandwidth_combination_set_eutra);

        $comboComponents = $combo->lteComponents;
        $this->assertSame(1, $comboComponents->count());

        $this->assertSame(Arr::except($comboComponents->first()->getAttributes(), 'id'), [
            'band'            => 7,
            'dl_class'        => 'C',
            'ul_class'        => 'C',
            'component_index' => 0,
        ]);

        /** @var LteComponent */
        $cc = $comboComponents->first();

        // Mimos

        $this->assertSame($cc->dl_mimos()->count(), 2);
        $this->assertSame($cc->ul_mimos()->count(), 2);

        $dlMimos = $cc->dl_mimos()->get()->map(function (Mimo $mimo) {
            return $mimo->mimo;
        })->values()->toArray();
        $this->assertEqualsCanonicalizing([2, 4], $dlMimos);

        $ulMimos = $cc->ul_mimos()->get()->map(function (Mimo $mimo) {
            return $mimo->mimo;
        })->values()->toArray();
        $this->assertEqualsCanonicalizing([1, 2], $ulMimos);

        // Modulation

        $this->assertSame($cc->dl_modulations()->count(), 2);
        $this->assertSame($cc->ul_modulations()->count(), 2);

        $dlMimos = $cc->dl_modulations()->get()->map(function (Modulation $mod) {
            return $mod->modulation;
        })->values()->toArray();
        $this->assertEqualsCanonicalizing(['qam256', 'qam1024'], $dlMimos);

        $ulMimos = $cc->ul_modulations()->get()->map(function (Modulation $mod) {
            return $mod->modulation;
        })->values()->toArray();
        $this->assertEqualsCanonicalizing(['qam64', 'qam256'], $ulMimos);

        // ##############################
        // Combo 4
        // ##############################

        /** @var Combo */
        $combo = $combos->get(3);

        $this->assertArraySubset([
            'combo_string'      => '1A4A',
            'capability_set_id' => $testingCapabilitySet->id,
        ], $combo->getAttributes());
        $this->assertSame(['all'], $combo->bandwidth_combination_set_eutra);

        $comboComponents = $combo->lteComponents;
        $this->assertSame(1, $comboComponents->count());

        $this->assertSame(Arr::except($comboComponents->first()->getAttributes(), 'id'), [
            'band'            => 1,
            'dl_class'        => 'A',
            'ul_class'        => 'A',
            'component_index' => 0,
        ]);

        /** @var LteComponent */
        $cc = $comboComponents->first();

        // Modulation

        $this->assertSame($cc->dl_modulations()->count(), 1);
        $this->assertSame($cc->ul_modulations()->count(), 1);

        $dlMimos = $cc->dl_modulations()->get()->map(function (Modulation $mod) {
            return $mod->modulation;
        })->values()->toArray();
        $this->assertEqualsCanonicalizing(['qam256'], $dlMimos);

        $ulMimos = $cc->ul_modulations()->get()->map(function (Modulation $mod) {
            return $mod->modulation;
        })->values()->toArray();
        $this->assertEqualsCanonicalizing(['qam64'], $ulMimos);

        // ##############################
        // Combo 5
        // ##############################

        /** @var Combo */
        $combo = $combos->get(4);

        $this->assertArraySubset([
            'combo_string'      => '1A4A2-3-32A',
            'capability_set_id' => $testingCapabilitySet->id,
        ], $combo->getAttributes());
        $this->assertSame(null, $combo->bandwidth_combination_set_eutra);

        $comboComponents = $combo->lteComponents;
        $this->assertSame(3, $comboComponents->count());

        $this->assertSame(Arr::except($comboComponents->get(0)->getAttributes(), 'id'), [
            'band'            => 1,
            'dl_class'        => 'A',
            'ul_class'        => 'A',
            'component_index' => 0,
        ]);

        $this->assertSame(Arr::except($comboComponents->get(1)->getAttributes(), 'id'), [
            'band'            => 3,
            'dl_class'        => null,
            'ul_class'        => null,
            'component_index' => 1,
        ]);

        $this->assertSame(Arr::except($comboComponents->get(2)->getAttributes(), 'id'), [
            'band'            => 32,
            'dl_class'        => 'A',
            'ul_class'        => null,
            'component_index' => 2,
        ]);

        /** @var LteComponent */
        $cc = $comboComponents->get(1);

        // Mimos

        $this->assertSame($cc->dl_mimos()->count(), 0);
        $this->assertSame($cc->ul_mimos()->count(), 0);

        // Modulation

        $this->assertSame($cc->dl_modulations()->count(), 0);
        $this->assertSame($cc->ul_modulations()->count(), 0);

        /** @var LteComponent */
        $cc = $comboComponents->get(2);

        // Mimos

        $this->assertSame($cc->dl_mimos()->count(), 0);
        $this->assertSame($cc->ul_mimos()->count(), 0);

        // Modulation

        $this->assertSame($cc->dl_modulations()->count(), 0);
        $this->assertSame($cc->ul_modulations()->count(), 0);
    }
}
