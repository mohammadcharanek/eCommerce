<?php

namespace ECommerce\Coupon\Services;

use ECommerce\Coupon\Models\Coupon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CouponService
{
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Coupon::paginate($perPage);
    }

    public function create(array $data): Coupon
    {
        $data['code'] = strtoupper($data['code'] ?? Str::random(8));
        return Coupon::create($data);
    }

    public function update(int $id, array $data): Coupon
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update($data);
        return $coupon->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) Coupon::findOrFail($id)->delete();
    }

    public function validate(string $code, float $orderAmount): Coupon
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon) {
            throw new \RuntimeException('Coupon code not found.');
        }

        if (!$coupon->isValid()) {
            throw new \RuntimeException('Coupon is not valid or has expired.');
        }

        if ($coupon->min_order_amount && $orderAmount < $coupon->min_order_amount) {
            throw new \RuntimeException("Minimum order amount of {$coupon->min_order_amount} required.");
        }

        return $coupon;
    }

    public function apply(string $code, float $orderAmount): array
    {
        $coupon = $this->validate($code, $orderAmount);
        $discount = $coupon->calculateDiscount($orderAmount);

        return [
            'coupon'   => $coupon,
            'discount' => $discount,
        ];
    }

    public function getDiscount(Coupon $coupon, float $orderAmount): float
    {
        return $coupon->calculateDiscount($orderAmount);
    }
}
