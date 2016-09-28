<?php

return [
    'staff' => [
        [
            'title' => '功能管理',
            'icon' => '<span class="ion-ios-cog" style="font-size: 52px;"></span>',
            'type'  => 'item',
            'route' => 'feature.manage'
        ],
        [
            'title' => '地区/公司',
            'icon' => '<span class="ion-earth"></span>',
            'type'  => 'parent',
            'item' => [
                [
                    'title' => '地区管理',
                    'icon' => '<span class="ion-map"></span>',
                    'type'  => 'item',
                    'route' => 'area.manage'
                ],
                [
                    'title' => '分子公司',
                    'icon' => '<span class="ion-android-pin"></span>',
                    'type'  => 'item',
                    'route' => 'company.manage'
                ],
            ]
        ],
        [
            'title' => '公司信息',
            'icon' => '<span class="ion-home"></span>',
            'type'  => 'item',
            'route' => 'company.information'
        ],
        [
            'title' => '到货方式',
            'icon' => '<span class="ion-plane"></span>',
            'type'  => 'item',
            'route' => 'company.delivery_mode.manage'
        ],
        [
            'title' => '角色管理',
            'icon' => '<span class="ion-tshirt"></span>',
            'type'  => 'item',
            'route' => 'role.manage'
        ],
        [
            'title' => '员工帐号',
            'icon' => '<span class="ion-person"></span>',
            'type'  => 'item',
            'route' => 'user.staff.manage'
        ],
        [
            'title' => '原料目录',
            'icon' => '<span class="ion-cube"></span>',
            'type'  => 'parent',
            'item' => [
                [
                    'title' => '原料类别',
                    'icon' => '<span class="ion-grid"></span>',
                    'type'  => 'item',
                    'route' => 'goods.category.manage'
                ],
                [
                    'title' => '原料目录',
                    'icon' => '<span class="ion-soup-can"></span>',
                    'type'  => 'item',
                    'route' => 'goods.manage'
                ]
            ]
        ],
        [
            'title' => '系统设置',
            'icon' => '<span class="ion-settings"></span>',
            'type'  => 'parent',
            'item' => [
                [
                    'title' => '审核流程',
                    'icon' => '<span class="ion-network"></span>',
                    'type'  => 'item',
                    'route' => 'setting.check_flow'
                ],
                [
                    'title' => '分配规则',
                    'icon' => '<span class="ion-funnel"></span>',
                    'type'  => 'item',
                    'route' => 'setting.assign_rule'
                ],
                [
                    'title' => '流标条件',
                    'icon' => '<span class="ion-arrow-graph-down-right"></span>',
                    'type'  => 'item',
                    'route' => 'setting.offer_min'
                ],
            ]
        ],
        [
            'title' => '供应商',
            'icon'  => '<span class="ion-android-boat"></span>',
            'type'  => 'parent',
            'item' => [
                [
                    'title' => '供应商属性',
                    'icon'  => '<span class="ion-ios-browsers"></span>',
                    'type'  => 'item',
                    'route' => 'user.supplier.addition.index'
                ],
                [
                    'title' => '供应商目录',
                    'icon'  => '<span class="ion-ios-box"></span>',
                    'type'  => 'item',
                    'route' => 'user.supplier.manage'
                ],
                [
                    'title' => '供应商共享',
                    'icon'  => '<span class="ion-social-buffer"></span>',
                    'type'  => 'item',
                    'route' => 'user.supplier.share'
                ]
            ]
        ],
        [
            'title' => '采购需求',
            'icon' => '<span class="ion-filing"></span>',
            'type'  => 'parent',
            'item' => [
                [
                    'title' => '需求记录',
                    'icon' => '<span class="ion-ios-calendar"></span>',
                    'type'  => 'item',
                    'route' => 'demand.staff.manage'
                ],
                [
                    'title' => '需求审核',
                    'icon' => '<span class="ion-checkmark-circled"></span>',
                    'type'  => 'item',
                    'route' => 'demand.check.index'
                ]
            ]
        ],
        [
            'title' => '需求汇总',
            'icon' => '<span class="ion-filing"></span>',
            'type'  => 'item',
            'route' => 'bid.demand.index'
        ],
        [
            'title' => '汇总审核',
            'icon' => '<span class="ion-android-checkmark-circle"></span>',
            'type'  => 'item',
            'route' => 'bid.check.index'
        ],
        [
            'title' => '供应商报价',
            'icon' => '<span class="ion-ios-paper"></span>',
            'type'  => 'item',
            'route' => 'bid.company.index'
        ],
        [
            'title' => '采购合同',
            'icon' => '<span class="ion-ios-copy"></span>',
            'type' => 'item',
            'route' => 'contract.company.index'
        ],
        [
            'title' => '自定义短信',
            'icon' => '<span class="ion-android-textsms"></span>',
            'type' => 'item',
            'route' => 'sms.index'
        ],
        [
            'title' => '统计分析',
            'icon' => '<span class="ion-ios-pie"></span>',
            'type' => 'parent',
            'item' => [
                [
                    'title' => '累计统计',
                    'icon' => '<span class="ion-ios-albums"></span>',
                    'type' => 'item',
                    'route' => 'statistics.bid_count'
                ],
                [
                    'title' => '参与统计',
                    'icon' => '<span class="ion-ios-pie-outline"></span>',
                    'type' => 'item',
                    'route' => 'statistics.bid_rate'
                ],
                [
                    'title' => '比率统计',
                    'icon' => '<span class="ion-ios-analytics"></span>',
                    'type' => 'item',
                    'route' => 'statistics.supplier'
                ],
                [
                    'title' => '客户满意度',
                    'icon' => '<span class="ion-ios-analytics-outline"></span>',
                    'type' => 'item',
                    'route' => 'statistics.grade'
                ]
            ]
        ],
        [
            'title' => '询价单',
            'icon' => '<span class="ion-ios-pricetags"></span>',
            'type' => 'item',
            'route' => 'enquiry.staff.index'
        ],
        [
            'title' => '报价记录',
            'icon' => '<span class="ion-clipboard"></span>',
            'type' => 'item',
            'route' => 'offer.information.company.index'
        ]
    ],
    'supplier' => [
        [
            'title' => '采购标书',
            'icon' => '<span class="ion-ios-paper"></span>',
            'type'  => 'item',
            'route' => 'bid.supplier.index'
        ],
        [
            'title' => '待投标报价标书',
            'icon' => '<span class="ion-android-stopwatch"></span>',
            'type'  => 'item',
            'route' => 'bid.supplier.pending'
        ],
        [
            'title' => '已完成投标标书',
            'icon' => '<span class="ion-android-calendar"></span>',
            'type'  => 'item',
            'route' => 'bid.supplier.done'
        ],
        [
            'title' => '采购合同',
            'icon' => '<span class="ion-ios-copy"></span>',
            'type'  => 'item',
            'route' => 'contract.supplier.index'
        ],
        [
            'title' => '汇总统计',
            'icon' => '<span class="ion-ios-analytics"></span>',
            'type' => 'item',
            'route' => 'statistics.company'
        ],
        [
            'title' => '询价报价',
            'icon' => '<span class="ion-ios-pricetags"></span>',
            'type' => 'item',
            'route' => 'enquiry.supplier.index'
        ],
        [
            'title' => '日常报价',
            'icon' => '<span class="ion-clipboard"></span>',
            'type' => 'item',
            'route' => 'offer.information.index'
        ]
    ]
];