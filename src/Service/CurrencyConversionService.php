<?php

namespace App\Service;

class CurrencyConversionService
{
    // Taux de change approximatifs (en production, utiliser une API comme Fixer.io ou ExchangeRate-API)
    private array $exchangeRates = [
        'USD' => 1.0,
        'EUR' => 0.85,
        'GBP' => 0.73,
        'CAD' => 1.25,
        'AUD' => 1.35,
        'CHF' => 0.92,
        'JPY' => 110.0,
        'CNY' => 6.45,
        'INR' => 75.0,
        'BRL' => 5.2,
        'MAD' => 9.0,
        'AED' => 3.67,
        'SAR' => 3.75,
        'EGP' => 15.7,
        'ZAR' => 14.5,
        'KRW' => 1180.0,
        'SGD' => 1.35,
        'HKD' => 7.8,
        'NZD' => 1.4,
        'SEK' => 8.5,
        'NOK' => 8.7,
        'DKK' => 6.3,
        'PLN' => 3.9,
        'CZK' => 21.5,
        'HUF' => 300.0,
        'RON' => 4.2,
        'BGN' => 1.66,
        'HRK' => 6.4,
        'RSD' => 100.0,
        'TRY' => 8.5,
        'RUB' => 75.0,
        'UAH' => 27.0,
        'ILS' => 3.2,
        'QAR' => 3.64,
        'KWD' => 0.30,
        'BHD' => 0.38,
        'OMR' => 0.38,
        'JOD' => 0.71,
        'LBP' => 1500.0,
        'PKR' => 160.0,
        'BDT' => 85.0,
        'LKR' => 200.0,
        'NPR' => 120.0,
        'AFN' => 80.0,
        'THB' => 33.0,
        'VND' => 23000.0,
        'IDR' => 14500.0,
        'MYR' => 4.2,
        'PHP' => 50.0,
        'TWD' => 28.0,
        'MXN' => 20.0,
        'ARS' => 100.0,
        'CLP' => 800.0,
        'COP' => 3800.0,
        'PEN' => 3.7,
        'UYU' => 44.0,
        'PYG' => 7000.0,
        'BOB' => 6.9,
        'VES' => 4000000.0,
        'GYD' => 210.0,
        'SRD' => 21.0,
        'DZD' => 135.0,
        'TND' => 2.8,
        'LYD' => 4.5,
        'SDG' => 55.0,
        'ETB' => 45.0,
        'KES' => 110.0,
        'UGX' => 3500.0,
        'TZS' => 2300.0,
        'GHS' => 6.0,
        'NGN' => 410.0,
        'BWP' => 11.0,
        'NAD' => 14.5,
        'ZWL' => 360.0,
        'ZMW' => 18.0,
        'MWK' => 820.0,
        'MZN' => 64.0,
        'AOA' => 650.0,
        'XAF' => 550.0,
        'XOF' => 550.0,
        'CDF' => 2000.0,
        'STN' => 20.0,
        'GNF' => 10000.0,
        'SLE' => 11.0,
        'LRD' => 160.0,
        'GMD' => 52.0,
        'MRU' => 36.0,
        'CVE' => 100.0,
        'KMF' => 450.0,
        'SCR' => 13.5,
        'MUR' => 40.0,
        'MGA' => 4000.0,
        'LSL' => 14.5,
        'SZL' => 14.5,
        'BYN' => 2.5,
        'MDL' => 17.5,
        'GEL' => 3.1,
        'AMD' => 520.0,
        'AZN' => 1.7,
        'KZT' => 425.0,
        'UZS' => 10750.0,
        'KGS' => 84.0,
        'TJS' => 11.3,
        'TMT' => 3.5,
        'MVR' => 15.4,
        'MMK' => 1800.0,
        'LAK' => 9500.0,
        'KHR' => 4100.0,
        'BND' => 1.35,
        'MOP' => 8.0,
        'MNT' => 2850.0,
        'KPW' => 900.0,
        'YER' => 250.0,
        'IQD' => 1460.0,
        'IRR' => 42000.0,
        'SYP' => 2500.0,
        'ISK' => 130.0,
        'ALL' => 105.0,
        'MKD' => 52.0,
        'BAM' => 1.66
    ];

    public function convertToUSD(float $amount, string $fromCurrency): float
    {
        if ($fromCurrency === 'USD') {
            return $amount;
        }

        $rate = $this->exchangeRates[$fromCurrency] ?? 1.0;
        return $amount / $rate;
    }

    public function convertFromUSD(float $amount, string $toCurrency): float
    {
        if ($toCurrency === 'USD') {
            return $amount;
        }

        $rate = $this->exchangeRates[$toCurrency] ?? 1.0;
        return $amount * $rate;
    }

    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        // Convert to USD first, then to target currency
        $usdAmount = $this->convertToUSD($amount, $fromCurrency);
        return $this->convertFromUSD($usdAmount, $toCurrency);
    }

    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $fromRate = $this->exchangeRates[$fromCurrency] ?? 1.0;
        $toRate = $this->exchangeRates[$toCurrency] ?? 1.0;

        return $toRate / $fromRate;
    }

    public function isSupportedCurrency(string $currency): bool
    {
        return array_key_exists($currency, $this->exchangeRates);
    }

    public function getSupportedCurrencies(): array
    {
        return array_keys($this->exchangeRates);
    }
}
