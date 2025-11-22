<?php
namespace Sabau360\SDK\Security\Policy;

class Rule
{
    /**
     * You can view employee information, but you cannot make changes.
     * @var string
     */
    public const EMP_VIEWER = 'emp-viewer';

    /**
     * You can handle basic employee-related tasks, such as recording attendance or updating minor data.
     * @var string
     */
    public const EMP_OPERATOR = 'emp-operator';

    /**
     * You supervise personnel, approve requests, and coordinate team operations.
     * @var string
     */
    public const EMP_MANAGER = 'emp-manager';

    /**
     * You manage all employee information, including permissions, roles, and HR settings.
     * @var string
     */
    public const EMP_ADMIN = 'emp-admin';


    /**
     * Customer with access only to their own profile, orders, and purchase history.
     * @var string
     */
    public const CUS_SELF = 'cus-self';

    /**
     * Customer manager who can manage multiple customer profiles and review their orders or issues.
     * @var string
     */
    public const CUS_MANAGER = 'cus-manager';

    /**
     * Administrator with full control over customer management, including policies, pricing, and contracts.
     * @var string
     */
    public const CUS_ADMIN = 'cus-admin';

    /**
     * Supplier with access to their own orders, deliveries, and invoices.
     * @var string
     */
    public const SUP_SELF = 'sup-self';

    /**
     * Quality responsible who can review, approve, or reject supplier deliveries.
     * @var string
     */
    public const SUP_QA = 'sup-qa';

    /**
     * Supplier administrator with full control over contracts, approvals, and supplier relations.
     * @var string
     */
    public const SUP_ADMIN = 'sup-admin';


    /**
     * The development observer.
     * @var string
     */
    public const DEV_VIEWER = 'dev-viewer';

    /**
     * Developer who can contribute code, create branches, and propose changes.
     * @var string
     */
    public const DEV_CONTRIBUTOR = 'dev-contributor';

    /**
     * Responsible for reviewing, approving, and merging code contributions in the repository.
     * @var string
     */
    public const DEV_MAINTAINER = 'dev-maintainer';

    /**
     * Responsible for preparing, verifying, and releasing new software versions.
     * @var string
     */
    public const DEV_RELEASE = 'dev-release';


    /**
     * You can view logistics information such as shipments, routes, and delivery statuses.
     * @var string
     */
    public const LOG_VIEWER = 'log-viewer';

    /**
     * You manage daily logistics operations, create and update transport orders, and coordinate deliveries.
     * @var string
     */
    public const LOG_OPERATOR = 'log-operator';

    /**
     * You supervise logistics operations, plan routes, and control transport efficiency.
     * @var string
     */
    public const LOG_MANAGER = 'log-manager';

    /**
     * Logistics system administrator with full control over settings, integrations, and user management.
     * @var string
     */
    public const LOG_ADMIN = 'log-admin';


}
