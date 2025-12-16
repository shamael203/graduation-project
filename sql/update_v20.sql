USE book_exchange;

-- تعديل عمود التاريخ في جدول الرسائل ليكون متوافق مع MySQL الحديثة
ALTER TABLE messages
MODIFY `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- إضافة عمود seen لتتبع حالة قراءة الرسائل
ALTER TABLE messages
ADD COLUMN `seen` TINYINT(1) NOT NULL DEFAULT 0 AFTER book_id;

-- مثال: لو أضفت فهرس جديد على جدول الكتب
ALTER TABLE books
ADD INDEX idx_category (category);

-- مثال: لو عدلت عمود في جدول profile
ALTER TABLE profile
MODIFY `phone` VARCHAR(20) NOT NULL;

-- مثال: لو أضفت عمود جديد في جدول users
ALTER TABLE users
ADD COLUMN `status` VARCHAR(20) DEFAULT 'active' AFTER email;
