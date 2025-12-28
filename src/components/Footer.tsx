import { Phone, Mail, MapPin, Clock, MessageCircle } from 'lucide-react';

export function Footer() {
  return (
    <footer className="bg-gray-900 text-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-12 mb-8 sm:mb-12">
          {/* Company Info */}
          <div>
            <div className="mb-6">
              <div className="text-xl font-bold text-white mb-2">
                ALARM<span className="text-emerald-500">STYLE</span>
              </div>
              <div className="text-sm text-gray-400 uppercase tracking-wide">Охранные системы</div>
            </div>
            <p className="text-gray-400 mb-6 leading-relaxed text-sm sm:text-base">
              Профессиональная установка автомобильных охранных систем с гарантией качества
            </p>
          </div>

          {/* Services */}
          <div>
            <h3 className="font-bold text-white mb-4 sm:mb-6">Услуги</h3>
            <ul className="space-y-2 sm:space-y-3">
              <li>
                <a href="#" className="text-sm sm:text-base text-gray-400 hover:text-emerald-400 transition">
                  Автосигнализации
                </a>
              </li>
              <li>
                <a href="#" className="text-sm sm:text-base text-gray-400 hover:text-emerald-400 transition">
                  GPS-трекеры
                </a>
              </li>
              <li>
                <a href="#" className="text-sm sm:text-base text-gray-400 hover:text-emerald-400 transition">
                  Парктроники
                </a>
              </li>
              <li>
                <a href="#" className="text-sm sm:text-base text-gray-400 hover:text-emerald-400 transition">
                  Камеры заднего вида
                </a>
              </li>
              <li>
                <a href="#" className="text-sm sm:text-base text-gray-400 hover:text-emerald-400 transition">
                  Иммобилайзеры
                </a>
              </li>
            </ul>
          </div>

          {/* Company */}
          <div>
            <h3 className="font-bold text-white mb-4 sm:mb-6">Компания</h3>
            <ul className="space-y-2 sm:space-y-3">
              <li>
                <a href="#" className="text-sm sm:text-base text-gray-400 hover:text-emerald-400 transition">
                  О нас
                </a>
              </li>
              <li>
                <a href="#" className="text-sm sm:text-base text-gray-400 hover:text-emerald-400 transition">
                  Наши работы
                </a>
              </li>
              <li>
                <a href="#" className="text-sm sm:text-base text-gray-400 hover:text-emerald-400 transition">
                  Отзывы
                </a>
              </li>
              <li>
                <a href="#" className="text-sm sm:text-base text-gray-400 hover:text-emerald-400 transition">
                  Гарантии
                </a>
              </li>
              <li>
                <a href="#" className="text-sm sm:text-base text-gray-400 hover:text-emerald-400 transition">
                  Контакты
                </a>
              </li>
            </ul>
          </div>

          {/* Contacts */}
          <div>
            <h3 className="font-bold text-white mb-4 sm:mb-6">Контакты</h3>
            <ul className="space-y-3 sm:space-y-4">
              <li>
                <a href="tel:+74994441439" className="flex items-start gap-3 text-sm sm:text-base text-gray-400 hover:text-emerald-400 transition">
                  <Phone className="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 mt-0.5" />
                  <span>8 (499) 444-14-39</span>
                </a>
              </li>
              <li>
                <a href="mailto:alarm@style@mail.ru" className="flex items-start gap-3 text-sm sm:text-base text-gray-400 hover:text-emerald-400 transition">
                  <Mail className="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 mt-0.5" />
                  <span>alarm@style@mail.ru</span>
                </a>
              </li>
              <li>
                <div className="flex items-start gap-3 text-sm sm:text-base text-gray-400">
                  <MapPin className="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 mt-0.5" />
                  <span>Москва, пр-кт Вернадского, 124</span>
                </div>
              </li>
              <li>
                <div className="flex items-start gap-3 text-sm sm:text-base text-gray-400">
                  <Clock className="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 mt-0.5" />
                  <span>Ежедневно 10:00-21:00</span>
                </div>
              </li>
            </ul>

            {/* Social Links */}
            <div className="mt-6">
              <h4 className="font-bold text-white mb-4 text-sm sm:text-base">Мессенджеры</h4>
              <div className="flex gap-3">
                <a
                  href="https://wa.me/74994441439"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-600 hover:bg-emerald-500 transition-colors"
                  aria-label="WhatsApp"
                >
                  <MessageCircle className="w-5 h-5" />
                </a>
                <a
                  href="https://t.me/alarmstyle"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-600 hover:bg-emerald-500 transition-colors"
                  aria-label="Telegram"
                >
                  <MessageCircle className="w-5 h-5" />
                </a>
              </div>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="border-t border-gray-800 pt-6 sm:pt-8">
          <div className="flex flex-col md:flex-row justify-between items-center gap-4">
            <p className="text-gray-400 text-xs sm:text-sm text-center md:text-left">
              © 2024 AlarmStyle. Все права защищены.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 sm:gap-6">
              <a href="#" className="text-gray-400 hover:text-emerald-400 text-xs sm:text-sm transition text-center">
                Политика конфиденциальности
              </a>
              <a href="#" className="text-gray-400 hover:text-emerald-400 text-xs sm:text-sm transition text-center">
                Публичная оферта
              </a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}

