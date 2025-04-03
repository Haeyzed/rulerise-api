<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
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
             * The unique identifier for the subscription plan.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The name of the subscription plan.
             *
             * @var string $name
             * @example "Premium Plan"
             */
            'name' => $this->name,

            /**
             * The URL-friendly slug of the subscription plan.
             *
             * @var string $slug
             * @example "premium-plan"
             */
            'slug' => $this->slug,

            /**
             * The description of the subscription plan.
             *
             * @var string|null $description
             * @example "Our most popular plan for growing businesses"
             */
            'description' => $this->description,

            /**
             * The price of the subscription plan.
             *
             * @var float $price
             * @example 99.99
             */
            'price' => $this->price,

            /**
             * The currency of the subscription plan price.
             *
             * @var string $currency
             * @example "USD"
             */
            'currency' => $this->currency,

            /**
             * The formatted price of the subscription plan.
             *
             * @var string $formatted_price
             * @example "USD 99.99"
             */
            'formatted_price' => $this->formatted_price,

            /**
             * The duration of the subscription plan in days.
             *
             * @var int $duration_days
             * @example 30
             */
            'duration_days' => $this->duration_days,

            /**
             * The formatted duration of the subscription plan.
             *
             * @var string $formatted_duration
             * @example "1 month"
             */
            'formatted_duration' => $this->formatted_duration,

            /**
             * The limit of job posts for the subscription plan.
             *
             * @var int|null $job_posts_limit
             * @example 20
             */
            'job_posts_limit' => $this->job_posts_limit,

            /**
             * The limit of featured jobs for the subscription plan.
             *
             * @var int|null $featured_jobs_limit
             * @example 5
             */
            'featured_jobs_limit' => $this->featured_jobs_limit,

            /**
             * The limit of resume views for the subscription plan.
             *
             * @var int|null $resume_views_limit
             * @example 100
             */
            'resume_views_limit' => $this->resume_views_limit,

            /**
             * Whether the subscription plan is active.
             *
             * @var bool $is_active
             * @example true
             */
            'is_active' => $this->is_active,

            /**
             * The timestamp when the subscription plan was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the subscription plan was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

