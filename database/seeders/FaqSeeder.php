<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faq::truncate();
        $faqs = [
            [
                'question' => [
                    'en' => 'How can I order from the app?',
                    'ar' => 'كيف يمكنني الطلب من التطبيق؟'
                ],
                'answer' => [
                    'en' => 'You can choose items from the menu and add them to the cart to complete the order.',
                    'ar' => 'يمكنك اختيار الأصناف من قائمة الطعام وإضافتها إلى السلة ثم إتمام الطلب.'
                ]
            ],
            [
                'question' => [
                    'en' => 'Can I book a table?',
                    'ar' => 'هل يمكنني حجز طاولة؟'
                ],
                'answer' => [
                    'en' => 'Yes, you can book through the reservations section by selecting date and time.',
                    'ar' => 'نعم، يمكنك الحجز من خلال قسم الحجوزات وتحديد الوقت والتاريخ.'
                ]
            ],
            [
                'question' => [
                    'en' => 'Can I edit my order after placing it?',
                    'ar' => 'هل يمكنني تعديل طلبي بعد الإرسال؟'
                ],
                'answer' => [
                    'en' => 'No, but you can cancel it within 5 minutes after placing it.',
                    'ar' => 'لا يمكن تعديل الطلب بعد الإرسال، ولكن يمكنك إلغاؤه خلال 5 دقائق من الطلب.'
                ]
            ],
            [
                'question' => [
                    'en' => 'What payment methods are available?',
                    'ar' => 'ما طرق الدفع المتاحة؟'
                ],
                'answer' => [
                    'en' => 'You can pay by cash on delivery or by credit card.',
                    'ar' => 'الدفع عند الاستلام أو من خلال البطاقات البنكية.'
                ]
            ],
            [
                'question' => [
                    'en' => 'Do you offer vegetarian options?',
                    'ar' => 'هل توجد خيارات نباتية؟'
                ],
                'answer' => [
                    'en' => 'Yes, we have a special section for vegetarian meals.',
                    'ar' => 'نعم، لدينا قسم خاص بالأطعمة النباتية.'
                ]
            ],
            [
                'question' => [
                    'en' => 'How can I use coupons?',
                    'ar' => 'كيف أستخدم الكوبونات؟'
                ],
                'answer' => [
                    'en' => 'Enter the coupon code during checkout to apply the discount.',
                    'ar' => 'أدخل رمز الكوبون في صفحة الدفع للحصول على الخصم.'
                ]
            ],
            [
                'question' => [
                    'en' => 'Can I order as a guest?',
                    'ar' => 'هل يمكنني الطلب كزائر؟'
                ],
                'answer' => [
                    'en' => 'Yes, you can place an order without logging in using a temporary token.',
                    'ar' => 'نعم، يمكن الطلب بدون تسجيل الدخول باستخدام رمز مؤقت.'
                ]
            ],
            [
                'question' => [
                    'en' => 'How long does delivery take?',
                    'ar' => 'كم يستغرق توصيل الطلب؟'
                ],
                'answer' => [
                    'en' => 'Delivery takes 20 to 45 minutes depending on your location.',
                    'ar' => 'يختلف حسب الموقع، ويتراوح من 20 إلى 45 دقيقة.'
                ]
            ],
            [
                'question' => [
                    'en' => 'Can I schedule a delivery?',
                    'ar' => 'هل يمكنني تحديد وقت استلام الطلب؟'
                ],
                'answer' => [
                    'en' => 'Yes, you can choose a specific time during checkout.',
                    'ar' => 'نعم، يمكنك اختيار وقت محدد للاستلام أثناء الطلب.'
                ]
            ],
            [
                'question' => [
                    'en' => 'How do I track my order?',
                    'ar' => 'كيف أعرف حالة طلبي؟'
                ],
                'answer' => [
                    'en' => 'You can track your order status from the "My Orders" section.',
                    'ar' => 'يمكنك تتبع حالة الطلب من خلال قسم "طلباتي" في التطبيق.'
                ]
            ],
            [
                'question' => [
                    'en' => 'What is the minimum order amount?',
                    'ar' => 'ما هو الحد الأدنى للطلب؟'
                ],
                'answer' => [
                    'en' => 'The minimum order is €5.',
                    'ar' => 'الحد الأدنى للطلب هو 5 يورو.'
                ]
            ],
            [
                'question' => [
                    'en' => 'Can I cancel a reservation?',
                    'ar' => 'هل يمكنني إلغاء الحجز؟'
                ],
                'answer' => [
                    'en' => 'Yes, from the "My Reservations" section.',
                    'ar' => 'نعم، يمكنك إلغاء الحجز من خلال قسم "حجوزاتي".'
                ]
            ],
            [
                'question' => [
                    'en' => 'How can I contact support?',
                    'ar' => 'كيف يمكنني التواصل مع الدعم؟'
                ],
                'answer' => [
                    'en' => 'Through the "Support" section or our phone line.',
                    'ar' => 'من خلال صفحة "الدعم الفني" أو الاتصال بنا عبر الهاتف.'
                ]
            ],
            [
                'question' => [
                    'en' => 'Is the app available on Android and iOS?',
                    'ar' => 'هل التطبيق متاح على iOS وAndroid؟'
                ],
                'answer' => [
                    'en' => 'Yes, available on both platforms.',
                    'ar' => 'نعم، التطبيق متاح على كلا النظامين.'
                ]
            ],
            [
                'question' => [
                    'en' => 'Do you offer special deals?',
                    'ar' => 'هل تقدمون عروض خاصة؟'
                ],
                'answer' => [
                    'en' => 'Yes, weekly deals are posted in the offers section.',
                    'ar' => 'نعم، نقوم بإضافة عروض أسبوعية في قسم العروض.'
                ]
            ],
            [
                'question' => [
                    'en' => 'What are your working hours?',
                    'ar' => 'ما هي ساعات العمل؟'
                ],
                'answer' => [
                    'en' => 'From 10 AM to 11 PM daily.',
                    'ar' => 'من 10 صباحاً حتى 11 مساءً يومياً.'
                ]
            ],
            [
                'question' => [
                    'en' => 'Can I reorder previous orders?',
                    'ar' => 'هل يمكنني طلب نفس الطلب مرة أخرى؟'
                ],
                'answer' => [
                    'en' => 'Yes, from the "My Orders" section.',
                    'ar' => 'نعم، يمكنك إعادة طلب أي طلب سابق من قسم "طلباتي".'
                ]
            ],
            [
                'question' => [
                    'en' => 'Can I choose a specific branch to order from?',
                    'ar' => 'هل أستطيع الطلب من فرع معين؟'
                ],
                'answer' => [
                    'en' => 'Yes, you can select a branch during the order process.',
                    'ar' => 'نعم، يمكنك تحديد الفرع أثناء عملية الطلب.'
                ]
            ],
            [
                'question' => [
                    'en' => 'Is delivery free?',
                    'ar' => 'هل توصيل الطعام مجاني؟'
                ],
                'answer' => [
                    'en' => 'Delivery is free for orders over €20.',
                    'ar' => 'التوصيل مجاني للطلبات فوق 20 يورو.'
                ]
            ],
            [
                'question' => [
                    'en' => 'Will my address be saved for future orders?',
                    'ar' => 'هل يتم حفظ عنواني تلقائياً؟'
                ],
                'answer' => [
                    'en' => 'Yes, to make future orders faster.',
                    'ar' => 'نعم، يتم حفظ العنوان لتسهيل الطلبات المستقبلية.'
                ]
            ],
        ];

        foreach ($faqs as $faq)
            Faq::create($faq);
    }
}
