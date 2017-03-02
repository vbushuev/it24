select 
    goods.id AS id,
    goods.sid AS sid,
    goods.timestamp AS timestamp,
    goods.title AS title,
    goods.sku AS sku,
    goods.description AS description,
    goods.certificate AS certificate,
    goods.barcode AS barcode,
    goods.image AS image,
    goods.unit AS unit,
    goods.pack AS pack,
    goods.weight AS weight,
    goods.width AS width,
    goods.depth AS depth,
    goods.height AS height,
    goods.brand_id AS brand_id,
    goods.supply_id AS supply_id,
    goods.price AS price,
    goods_categories.category_id AS category_id,
    brands.title AS brand,
    categories.title AS category,
    suppliers.title AS supplier
from goods
    join brands on brands.id = goods.brand_id
    join suppliers on suppliers.id = goods.supply_id
    join goods_categories on goods_categories.good_id = goods.id
    join categories on categories.id = goods_categories.category_id
order by goods.id;
