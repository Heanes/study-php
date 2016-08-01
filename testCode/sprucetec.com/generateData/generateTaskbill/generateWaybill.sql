/**
 * @doc 插入到运单表
 * @author fanggang
 * @time 2016-07-26 18:20:10 周二
 */
insert into waybill(
    waybill_no, omc_order_id, origin_order_id
    , origin_order_type, order_source, order_mark
    , is_coldchain, is_bulkorder, is_allocation
    , delivery_time, dist_service_id, batch_id, distribution_type
    , total_weight, total_volume, total_amount, remark
    , warehouse_id, warehouse_name, city_id, city_name, region_id, station_region_id, site_id, site_type
    , total_price, coupon_discount, payable_amount, balance_money, paid_money, distribute_payable_amount
    , saller_id, saller_name, saller_phone
    , expect_receive_time
    , is_deleted, c_t, u_t, create_user, update_user
)
    values
        (
            '19345281288195600', '19345281288195600', '19345281288195600'
            , '1', '0', ''
            , '0', '0', '0'
            , '1467216000', '2', '24', '10'
            , '0.00', '0.00', '0.00', ''
            , '1', '北京仓库1', '1', '北京', '3899', '3899', '0', '0'
            , '119.00', '0.00', '119.00', '0.00', '0.00', '0.00'
            , '12', '雷亚斌', '18813120953'
            , ''
            , '0', '1467271808', '1467701561', '999', '999'
        )
;

/**
 * @doc 插入到运单状态表
 * @author fanggang
 * @time 2016-07-26 18:40:10 周二
 */
insert into waybill_status(
    waybill_no, waybill_status, pay_status, city_id, sorting_status
    , is_deleted , c_t, u_t, create_user, update_user
)
    values
        (
            '16041121540788', '100', '0', '1', '0'
            , '0', '1460714378', '0', '999', '0'
        )
;

/**
 * @doc 插入到运单状态流水表
 * @author fanggang
 * @time 2016-07-26 19:07:27 周二
 */
insert into waybill_status_flow(
    waybill_no, waybill_status
    , operate_time, operator, operator_name, operate_type, operate_desc, city_id
    , is_deleted, c_t, u_t, create_user, update_user
)
    values
        (
            '19272980707329400', '100'
            , '1467097329', '999', '系统', '100', '已接单', '1'
            , '0', '1467097329', '1467097329', '999', '0'
        )
;

/**
 * @doc 插入到运单收货信息表
 * @author fanggang
 * @time 2016-07-26 19:16:02 周二
 */
insert into waybill_receive_info(
    waybill_no, receive_customer_id, receive_customer_name, receive_customer_type, receiver_name, receiver_phone
    , gov_province_code, gov_province_name, gov_city_code, gov_city_name, gov_county_code, gov_county_name, gov_address
    , receive_longitude, receive_latitude, city_id
    , is_deleted, c_t, u_t, create_user, update_user
)
    values
        (
            '16041525102840', '345139', '坪亭寿司(连锁)', '0', '王先生', '18612808217'
            , '', '', '', '', '', '', '成慧路未来广场购物中心B1层'
            , '116.24400', '39.80400', '1'
            , '0', '1460717896', '0', '999', '0'
        )
;

/**
 * @doc 插入到运单订单详细表
 * @author fanggang
 * @time 2016-07-26 19:16:40 周二
 */
insert into waybill_oi_detail(
    waybill_no, omc_item_id, omc_order_id, origin_item_id, origin_order_id
    , delivery_time, batch_id, ssu_id, sku_id, bi_name, sku_format
    , class1_id, class1_name, class2_id, class2_name, class3_id, class3_name
    , sku_level, sku_own_brand, ssu_unit, sku_price_unit, sku_physical_count
    , ssu_fp, ssu_name, price, real_price
    , expect_weight, expect_num, sorting_weight, sorting_num, sorting_time, shipment_weight, shipment_num, shipment_time
    , distribution_weight, distribution_num, return_warehouse_num, receive_person, receive_time
    , STATUS, online_pay_amount, city_id, length, width, high, weight
    , is_deleted, c_t, u_t, create_user, update_user, distribution_amount
)
    values
        (
            '19272969782117600', '19272995073688300', '19272969782117600', '192729950736883', '19272969782117600'
            , '1467129600', '24', '242844', '16711', '美菜娃娃菜', '袋'
            , '1', '蔬菜', '1', '叶菜类', '1', '青菜类'
            , '0', '集市★美菜', '包', '34', '0'
            , '0', '美菜娃娃菜', '16.00', '98.00'
            , '2.000', '1', '0.000', '0', '0', '0.000', '0', '0'
            , '0.000', '0', '0.00', '0', '0'
            , '100', '0.00', '1', '0.00', '0.00', '0.00', '0.000'
            , '0', '1467105198', '1467105198', '999', '999', '0.00'
        )
;

/**
 * @doc 插入到任务单表
 * @author fanggang
 * @time 2016-07-26 20:13:07 周二
 */
insert into taskbill(
    taskbill_no, task_type, main_taskbill_no, recommended_cartype_no, recommended_cartype_name, recommended_car_num
    , driver_no, driver_name, driver_phone, car_id, cartype_no, cartype_name, car_no
    , car_batch_no, dist_service_id, dist_service_name, batch_id
    , delivery_time, status, sorting_progress, is_issued
    , origin_type, origin_no, origin_name
    , gov_province_code, gov_province_name, gov_city_code, gov_city_name, gov_county_code, gov_county_name, gov_address
    , city_id, route_id, route_name, region_id, region_name, station_region_id, station_region_name
    , is_deleted, c_t, u_t, create_user, update_user
)
    values
        (
            'RZD000103530', '10', 'RZD000103530', 'CX11', '平顶金杯', '1'
            , '010000054003', '望京-3', '18976543210', '0', 'CX03', '车型3', '京P12349'
            , '', '2', '标准送', '24'
            , '1464710400', '33', '100', '0'
            , '10', '1', '北京仓库1'
            , '0', '', '0', '', '0', '', ''
            , '1', '106', '上地北-1', '0', '', '8', '上地北(站区)'
            , '0', '1464664378', '1464665549', '0', '1'
        )
;

/**
 * @doc 插入到任务单运单关系表
 * @author fanggang
 * @time 2016-07-26 20:22:35 周二
 */
insert into waybill_taskbill(
    waybill_no, waybill_type, main_taskbill_no, taskbill_no, task_type, city_id
    , is_deleted, c_t, u_t, create_user, update_user
)
    values
        (
            '179854500', '1', 'RZD000130032', 'RZD000130032', '10', '1'
            , '0', '1469417544', '1469418326', '1', '1'
        );