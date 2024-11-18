<?php

declare(strict_types=1);

namespace SAML2;

/**
 * Various SAML 2 constants.
 *
 * @package SimpleSAMLphp
 */
class Constants
{
    /**
     * Password authentication context.
     */
    const AC_PASSWORD = 'urn:oasis:names:tc:SAML:2.0:ac:classes:Password';

    /**
     * PasswordProtectedTransport authentication context.
     */
    const AC_PASSWORD_PROTECTED_TRANSPORT = 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport';

    /**
     * Unspecified authentication context.
     */
    const AC_UNSPECIFIED = 'urn:oasis:names:tc:SAML:2.0:ac:classes:unspecified';

    /**
     * Subject identifier attribute
     */
    const ATTR_SUBJECT_ID = 'urn:oasis:names:tc:SAML:attribute:subject-id';

    /**
     * Pairwise identifier attribute
     */
    const ATTR_PAIRWISE_ID = 'urn:oasis:names:tc:SAML:attribute:pairwise-id';

    /**
     * The URN for the HTTP-POST binding.
     */
    const BINDING_HTTP_POST = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST';

    /**
     * The URN for the HTTP-Redirect binding.
     */
    const BINDING_HTTP_REDIRECT = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect';

    /**
     * The URN for the HTTP-ARTIFACT binding.
     */
    const BINDING_HTTP_ARTIFACT = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact';

    /**
     * The URN for the SOAP binding.
     */
    const BINDING_SOAP = 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP';

    /**
     * The URN for the PAOS binding.
     */
    const BINDING_PAOS = 'urn:oasis:names:tc:SAML:2.0:bindings:PAOS';

    /**
     * The URN for the Holder-of-Key Web Browser SSO Profile binding
     */
    const BINDING_HOK_SSO = 'urn:oasis:names:tc:SAML:2.0:profiles:holder-of-key:SSO:browser';

    /**
     * Bearer subject confirmation method.
     */
    const CM_BEARER = 'urn:oasis:names:tc:SAML:2.0:cm:bearer';

    /**
     * Holder-of-Key subject confirmation method.
     */
    const CM_HOK = 'urn:oasis:names:tc:SAML:2.0:cm:holder-of-key';

    /**
     * Vouches subject confirmation method.
     */
    const CM_VOUCHES = 'urn:oasis:names:tc:SAML:2.0:cm:sender-vouches';

    /**
     * Request Authentication Context Comparison indicating that  the resulting authentication context in the
     * authentication statement MUST be stronger (as deemed by the responder) than any one of the authentication
     * contexts specified
     */
    const COMPARISON_BETTER = 'better';

    /**
     * Request Authentication Context Comparison indicating that the resulting authentication context in the
     * authentication statement MUST be the exact match of at least one of the authentication contexts specified
     */
    const COMPARISON_EXACT = 'exact';

    /**
     * Request Authentication Context Comparison indicating that the resulting authentication context in the
     * authentication statement MUST be as strong as possible (as deemed by the responder) without exceeding the
     * strength of at least one of the authentication contexts specified.
     */
    const COMPARISON_MAXIMUM = 'maximum';

    /**
     * Request Authentication Context Comparison indicating that he resulting authentication context in the
     * authentication statement MUST be at least as strong (as deemed by the responder) as one of the authentication
     * contexts specified.
     */
    const COMPARISON_MINIMUM = 'minimum';

    /**
     * No claim as to principal consent is being made.
     */
    const CONSENT_UNSPECIFIED = 'urn:oasis:names:tc:SAML:2.0:consent:unspecified';

    /**
     * Indicates that a principal’s consent has been obtained by the issuer of the message.
     */
    const CONSENT_OBTAINED = 'urn:oasis:names:tc:SAML:2.0:consent:obtained';

    /**
     * Indicates that a principal’s consent has been obtained by the issuer of the message at some point prior to
     * the action that initiated the message.
     */
    const CONSENT_PRIOR = 'urn:oasis:names:tc:SAML:2.0:consent:prior';

    /**
     * Indicates that a principal’s consent has been implicitly obtained by the issuer of the message during the
     * action that initiated the message, as part of a broader indication of consent.
     * Implicit consent is typically more proximal to the action in time and presentation than prior consent,
     * such as part of a session of activities.
     */
    const CONSENT_IMPLICIT = 'urn:oasis:names:tc:SAML:2.0:consent:current-implicit';

    /**
     * Indicates that a principal’s consent has been explicitly obtained by the issuer of the message during the
     * action that initiated the message.
     */
    const CONSENT_EXPLICIT = 'urn:oasis:names:tc:SAML:2.0:consent:current-explicit';

    /**
     * Indicates that the issuer of the message did not obtain consent.
     */
    const CONSENT_UNAVAILABLE = 'urn:oasis:names:tc:SAML:2.0:consent:unavailable';

    /**
     * Indicates that the issuer of the message does not believe that they need to obtain or report consent.
     */
    const CONSENT_INAPPLICABLE = 'urn:oasis:names:tc:SAML:2.0:consent:inapplicable';

    const EPTI_URN_MACE = 'urn:mace:dir:attribute-def:eduPersonTargetedID';

    const EPTI_URN_OID = 'urn:oid:1.3.6.1.4.1.5923.1.1.1.10';

    /**
     * LogoutRequest Reason - user wishes to terminate the session
     */
    public const LOGOUT_REASON_USER = 'urn:oasis:names:tc:SAML:2.0:logout:user';

    /**
     * LogoutRequest Reason - admin wishes to terminate the session
     */
    public const LOGOUT_REASON_ADMIN = 'urn:oasis:names:tc:SAML:2.0:logout:admin';

    /**
     * The interpretation of the attribute name is left to individual implementations.
     */
    const NAMEFORMAT_UNSPECIFIED = 'urn:oasis:names:tc:SAML:2.0:attrname-format:unspecified';

    /**
     * The attribute name follows the convention for URI references [RFC 2396], for example as used in XACML
     * [XACML] attribute identifiers. The interpretation of the URI content or naming scheme is application-
     * specific. See [SAMLProf] for attribute profiles that make use of this identifier.
     */
    const NAMEFORMAT_URI = 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri';

    /**
     * The class of strings acceptable as the attribute name MUST be drawn from the set of values belonging to
     * the primitive type xs:Name as defined in [Schema2] Section 3.3.6. See [SAMLProf] for attribute profiles
     * that make use of this identifier.
     */
    const NAMEFORMAT_BASIC = 'urn:oasis:names:tc:SAML:2.0:attrname-format:basic';

    /**
     * Unspecified NameID format.
     */
    const NAMEID_UNSPECIFIED = 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified';

    /**
     * Email address NameID format.
     */
    const NAMEID_EMAIL_ADDRESS = 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress';

    /**
     * X509 Subject Name NameID format.
     */
    const NAMEID_X509_SUBJECT_NAME = 'urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName';

    /**
     * Windows Domain Qualifier Name NameID format.
     */
    const NAMEID_WINDOWS_DOMAIN_QUALIFIED_NAME = 'urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName';

    /**
     * Kerberos Principal Name NameID format.
     */
    const NAMEID_KERBEROS = 'urn:oasis:names:tc:SAML:2.0:nameid-format:kerberos';

    /**
     * Entity NameID format.
     */
    const NAMEID_ENTITY = 'urn:oasis:names:tc:SAML:2.0:nameid-format:entity';

    /**
     * Persistent NameID format.
     */
    const NAMEID_PERSISTENT = 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent';

    /**
     * Transient NameID format.
     */
    const NAMEID_TRANSIENT = 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient';

    /**
     * Encrypted NameID format.
     */
    const NAMEID_ENCRYPTED = 'urn:oasis:names:tc:SAML:2.0:nameid-format:encrypted';

    /**
     * The namespace for the ECP protocol.
     */
    const NS_ECP = 'urn:oasis:names:tc:SAML:2.0:profiles:SSO:ecp';

    /**
     * The namespace for the SOAP protocol.
     */
    const NS_SOAP = 'http://schemas.xmlsoap.org/soap/envelope/';

    /**
     * The namespace for the SAML 2 protocol.
     */
    const NS_SAMLP = 'urn:oasis:names:tc:SAML:2.0:protocol';

    /**
     * The namespace for the SAML 2 assertions.
     */
    const NS_SAML = 'urn:oasis:names:tc:SAML:2.0:assertion';

    /**
     * The namespace for the SAML 2 metadata.
     */
    const NS_MD = 'urn:oasis:names:tc:SAML:2.0:metadata';

    /**
     * The namespace fox XML schema.
     */
    const NS_XS = 'http://www.w3.org/2001/XMLSchema';

    /**
     * The namespace for XML schema instance.
     */
    const NS_XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    /**
     * The namespace for the SAML 2 HoK Web Browser SSO Profile.
     */
    const NS_HOK = 'urn:oasis:names:tc:SAML:2.0:profiles:holder-of-key:SSO:browser';

    /**
     * The status namespace
     */
    const STATUS_PREFIX = 'urn:oasis:names:tc:SAML:2.0:status:';

    /**
     * Top-level status code indicating successful processing of the request.
     * The request succeeded. Additional information MAY be returned in the <StatusMessage>
     * and/or <StatusDetail> elements.
     *
     * Top-level status code.
     */
    const STATUS_SUCCESS = 'urn:oasis:names:tc:SAML:2.0:status:Success';

    /**
     * The request could not be performed due to an error on the part of the requester.
     *
     * Top-level status code.
     */
    const STATUS_REQUESTER = 'urn:oasis:names:tc:SAML:2.0:status:Requester';

    /**
     * The request could not be performed due to an error on the part of the SAML responder or SAML authority.
     *
     * Top-level status code.
     */
    const STATUS_RESPONDER = 'urn:oasis:names:tc:SAML:2.0:status:Responder';

    /**
     * The SAML responder could not process the request because the version of the request message was incorrect.
     *
     * Top-level status code.
     */
    const STATUS_VERSION_MISMATCH = 'urn:oasis:names:tc:SAML:2.0:status:VersionMismatch';

    /**
     * The responding provider was unable to successfully authenticate the principal.
     *
     * Second-level status code.
     */
    const STATUS_AUTHN_FAILED = 'urn:oasis:names:tc:SAML:2.0:status:AuthnFailed';

    /**
     * Unexpected or invalid content was encountered within a <saml:Attribute> or <saml:AttributeValue> element.
     *
     * Second-level status code.
     */
    const STATUS_INVALID_ATTR = 'urn:oasis:names:tc:SAML:2.0:status:InvalidAttrNameOrValue';

    /**
     * The responding provider cannot or will not support the requested name identifier policy.
     *
     * Second-level status code.
     */
    const STATUS_INVALID_NAMEID_POLICY = 'urn:oasis:names:tc:SAML:2.0:status:InvalidNameIDPolicy';

    /**
     * The specified authentication context requirements cannot be met by the responder.
     *
     * Second-level status code.
     */
    const STATUS_NO_AUTHN_CONTEXT = 'urn:oasis:names:tc:SAML:2.0:status:NoAuthnContext';

    /**
     * Used by an intermediary to indicate that none of the supported identity provider <Loc> elements in an
     * <IDPList> can be resolved or that none of the supported identity providers are available.
     *
     * Second-level status code.
     */
    const STATUS_NO_AVAILABLE_IDP = 'urn:oasis:names:tc:SAML:2.0:status:NoAvailableIDP';

    /**
     * Indicates the responding provider cannot authenticate the principal passively, as has been requested.
     *
     * Second-level status code.
     */
    const STATUS_NO_PASSIVE = 'urn:oasis:names:tc:SAML:2.0:status:NoPassive';

    /**
     * Used by an intermediary to indicate that none of the identity providers in an <IDPList> are
     * supported by the intermediary.
     *
     * Second-level status code.
     */
    const STATUS_NO_SUPPORTED_IDP = 'urn:oasis:names:tc:SAML:2.0:status:NoSupportedIDP';

    /**
     * Used by a session authority to indicate to a session participant that it was not able to propagate logout
     * to all other session participants.
     *
     * Second-level status code.
     */
    const STATUS_PARTIAL_LOGOUT = 'urn:oasis:names:tc:SAML:2.0:status:PartialLogout';

    /**
     * Indicates that a responding provider cannot authenticate the principal directly and is not permitted
     * to proxy the request further.
     *
     * Second-level status code.
     */
    const STATUS_PROXY_COUNT_EXCEEDED = 'urn:oasis:names:tc:SAML:2.0:status:ProxyCountExceeded';

    /**
     * The SAML responder or SAML authority is able to process the request but has chosen not to respond.
     * This status code MAY be used when there is concern about the security context of the request message or
     * the sequence of request messages received from a particular requester.
     *
     * Second-level status code.
     */
    const STATUS_REQUEST_DENIED = 'urn:oasis:names:tc:SAML:2.0:status:RequestDenied';

    /**
     * The SAML responder or SAML authority does not support the request.
     *
     * Second-level status code.
     */
    const STATUS_REQUEST_UNSUPPORTED = 'urn:oasis:names:tc:SAML:2.0:status:RequestUnsupported';

    /**
     * The SAML responder cannot process any requests with the protocol version specified in the request.
     *
     * Second-level status code.
     */
    const STATUS_REQUEST_VERSION_DEPRECATED = 'urn:oasis:names:tc:SAML:2.0:status:RequestVersionDeprecated';

    /**
     * The SAML responder cannot process the request because the protocol version specified in the request message
     * is a major upgrade from the highest protocol version supported by the responder.
     *
     * Second-level status code.
     */
    const STATUS_REQUEST_VERSION_TOO_HIGH = 'urn:oasis:names:tc:SAML:2.0:status:RequestVersionTooHigh';

    /**
     * The SAML responder cannot process the request because the protocol version specified in the request message
     * is too low.
     *
     * Second-level status code.
     */
    const STATUS_REQUEST_VERSION_TOO_LOW = 'urn:oasis:names:tc:SAML:2.0:status:RequestVersionTooLow';

    /**
     * The resource value provided in the request message is invalid or unrecognized.
     *
     * Second-level status code.
     */
    const STATUS_RESOURCE_NOT_RECOGNIZED = 'urn:oasis:names:tc:SAML:2.0:status:ResourceNotRecognized';

    /**
     * The response message would contain more elements than the SAML responder is able to return.
     *
     * Second-level status code.
     */
    const STATUS_TOO_MANY_RESPONSES = 'urn:oasis:names:tc:SAML:2.0:status:TooManyResponses';

    /**
     * An entity that has no knowledge of a particular attribute profile has been presented with an attribute
     * drawn from that profile.
     *
     * Second-level status code.
     */
    const STATUS_UNKNOWN_ATTR_PROFILE = 'urn:oasis:names:tc:SAML:2.0:status:UnknownAttrProfile';

    /**
     * The responding provider does not recognize the principal specified or implied by the request.
     *
     * Second-level status code.
     */
    const STATUS_UNKNOWN_PRINCIPAL = 'urn:oasis:names:tc:SAML:2.0:status:UnknownPrincipal';

    /**
     * The SAML responder cannot properly fulfill the request using the protocol binding specified in the request.
     *
     * Second-level status code.
     */
    const STATUS_UNSUPPORTED_BINDING = 'urn:oasis:names:tc:SAML:2.0:status:UnsupportedBinding';

    /**
     * The maximum size for any entityid as per specification
     */
    public const ENTITYID_MAX_LENGTH = 1024;

    /**
     * The maximum size for any entityid as per SAML2INT-specification
     */
    public const SAML2INT_ENTITYID_MAX_LENGTH = 256;
}
