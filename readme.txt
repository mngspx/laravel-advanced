# The largest heading

# Localization

 **Thư mục lưu trữ**
```--/resources
    --/lang
        --/en
            messages.php
        --/es
            messages.php
```
 - Các file trong gói ngôn ngữ có dạng
 vd:
     return [
         'welcome' => 'Welcome to our application'
     ];
 - Cấu hình gói ngôn ngữ mặc định tại đường dẫn config/app.php
   'locale' => 'en'
 - Cấu hình ngôn ngữ dự phòng khi ngôn ngữ mặc định không khả dụng bản dịch
   'fallback_locale' => 'en'
 - Có thể thay đổi ngôn ngữ khi chạy bằng cách sử dụng phương thức setLocale() trong App
     Route::get('welcome/{locale}', function ($locale) {
         App::setLocale($locale);

         //
     });
 - Sử dụng getLocale() xác định ngôn ngữ hiện tại
 - Sử dụng isLocale('name_locale') kiểm tra gói ngôn ngữ hiện tại có phải là name_locale không

 - Có thể sử dụng Json để lưu trữ ngôn ngữ lưu tại đường dẫn: /resources/lang/name_language.json

 - Cách khai báo sử dụng ngôn ngữ trong views
 {{ __('messages.key') }} or @lang('messages.key')// đối với lưu trữ dạng mảng
 {{ __('key') }} or @lang('key')// đối với json
 *) nếu không tồn tại chuỗi dịch sẽ tự động lấy key hiển thị


 - chèn 1 chuỗi nào đó vào chuỗi dịch sử dụng tiền tố :name
 trong đó name là tên chỗ muốn chèn
 vd:
 'welcome' => 'Welcome, :name',
 view:  __('messages.welcome', ['name' => 'nguyen']);


 - Phần Pluralization gần giống if else xem thêm tại https://laravel.com/docs/5.7/localization#overriding-package-language-files
 - Overriding Package Language Files
 Một số gói sẽ có gói ngôn ngữ riêng. để không phải sửa lại file gốc có thể thêm file muốn ghi đè vào đường dẫn resources/lang/vendor/{package}/{locale}
 Ví dụ:
 nếu bạn cần ghi đè các chuỗi dịch tiếng Anh trong messages.php cho một gói có tên là skyrim/hearthfire,
  bạn nên đặt một tệp ngôn ngữ tại: resources/lang/vendor/hearthfire/en/messages.php.
  Trong tệp này, bạn chỉ nên xác định các chuỗi dịch mà bạn muốn ghi đè.
  Mọi chuỗi dịch bạn không ghi đè sẽ vẫn được tải từ các tệp ngôn ngữ gốc của gói.
