<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanySubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier for the company subscription.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The company ID associated with this subscription.
             *
             * @var int $company_id
             * @example 1
             */
            'company_id' => $this->company_id,

            /**
             * The plan ID associated with this subscription.
             *
             * @var int $plan_id
             * @example 2
             */
            'plan_id' => $this->plan_id,

            /**
             * The subscription plan details.
             *
             * @var array|null $plan
             */
            'plan' => new SubscriptionPlanResource($this->whenLoaded('plan')),

            /**
             * The date and time when the subscription starts.
             *
             * @var string $starts_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'starts_at' => $this->starts_at,

            /**
             * The date and time when the subscription expires.
             *
             * @var string|null $expires_at
             * @example "2024-01-01T12:00:00.000000Z"
             */
            'expires_at' => $this->expires_at,

            /**
             * Whether the subscription is active.
             *
             * @var bool $is_active
             * @example true
             */
            'is_active' => $this->is_active,

            /**
             * The payment status of the subscription.
             *
             * @var string $payment_status
             * @example "completed"
             */
            'payment_status' => $this->payment_status,

            /**
             * The payment method used for the subscription.
             *
             * @var string|null $payment_method
             * @example "credit_card"
             */
            'payment_method' => $this->payment_method,

            /**
             * The transaction ID associated with the payment.
             *
             * @var string|null $transaction_id
             * @example "txn_1234567890"
             */
            'transaction_id' => $this->transaction_id,

            /**
             * The number of job posts used in this subscription.
             *
             * @var int $job_posts_used
             * @example 5
             */
            'job_posts_used' => $this->job_posts_used,

            /**
             * The number of featured jobs used in this subscription.
             *
             * @var int $featured_jobs_used
             * @example 2
             */
            'featured_jobs_used' => $this->featured_jobs_used,

            /**
             * The number of resume views used in this subscription.
             *
             * @var int $resume_views_used
             * @example 15
             */
            'resume_views_used' => $this->resume_views_used,

            /**
             * The number of days remaining in the subscription.
             *
             * @var int|null $remaining_days
             * @example 180
             */
            'remaining_days' => $this->remaining_days,

            /**
             * The number of job posts remaining in the subscription.
             *
             * @var int|null $job_posts_remaining
             * @example 15
             */
            'job_posts_remaining' => $this->job_posts_remaining,

            /**
             * The number of featured jobs remaining in the subscription.
             *
             * @var int|null $featured_jobs_remaining
             * @example 3
             */
            'featured_jobs_remaining' => $this->featured_jobs_remaining,

            /**
             * The number of resume views remaining in the subscription.
             *
             * @var int|null $resume_views_remaining
             * @example 85
             */
            'resume_views_remaining' => $this->resume_views_remaining,

            /**
             * Whether the subscription has expired.
             *
             * @var bool $is_expired
             * @example false
             */
            'is_expired' => $this->isExpired(),

            /**
             * The timestamp when the subscription was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the subscription was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

