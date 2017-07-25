delete from goods_categories;
update categories set parent_id=null;
delete from categories;
delete from uploads;
delete from upload_transactions;
delete from goods;
