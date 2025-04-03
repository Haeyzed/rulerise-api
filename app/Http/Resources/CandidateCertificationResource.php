<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateCertificationResource extends JsonResource
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
             * The unique identifier for the candidate certification.
             *
             * @var int $id
             * @example 1
             */
            'id' => $this->id,

            /**
             * The candidate ID associated with this certification.
             *
             * @var int $candidate_id
             * @example 1
             */
            'candidate_id' => $this->candidate_id,

            /**
             * The name of the certification.
             *
             * @var string $name
             * @example "AWS Certified Solutions Architect"
             */
            'name' => $this->name,

            /**
             * The organization that issued the certification.
             *
             * @var string $issuing_organization
             * @example "Amazon Web Services"
             */
            'issuing_organization' => $this->issuing_organization,

            /**
             * The date when the certification was issued.
             *
             * @var string|null $issue_date
             * @example "2022-05-15"
             */
            'issue_date' => $this->issue_date,

            /**
             * The date when the certification expires.
             *
             * @var string|null $expiration_date
             * @example "2025-05-15"
             */
            'expiration_date' => $this->expiration_date,

            /**
             * Whether the certification has expired.
             *
             * @var bool $is_expired
             * @example false
             */
            'is_expired' => $this->is_expired,

            /**
             * The validity period of the certification.
             *
             * @var string|null $validity_period
             * @example "May 2022 - May 2025"
             */
            'validity_period' => $this->validity_period,

            /**
             * The credential ID of the certification.
             *
             * @var string|null $credential_id
             * @example "AWS-ASA-123456"
             */
            'credential_id' => $this->credential_id,

            /**
             * The URL to verify the credential.
             *
             * @var string|null $credential_url
             * @example "https://aws.amazon.com/verification/123456"
             */
            'credential_url' => $this->credential_url,

            /**
             * The timestamp when the certification record was created.
             *
             * @var string $created_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'created_at' => $this->created_at,

            /**
             * The timestamp when the certification record was last updated.
             *
             * @var string $updated_at
             * @example "2023-01-01T12:00:00.000000Z"
             */
            'updated_at' => $this->updated_at,
        ];
    }
}

