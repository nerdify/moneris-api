<?php

namespace CraigPaul\Moneris;

/**
 * CraigPaul\Moneris\Vault
 *
 * @property-read string $environment
 * @property-read string $id
 * @property-read string $token
 */
class Vault extends Gateway
{
    use Gettable;

    /**
     * Create a new Vault instance.
     *
     * @param string $id
     * @param string $token
     * @param string $environment
     *
     * @return void
     */
    public function __construct(string $id, string $token, string $environment)
    {
        parent::__construct($id, $token, $environment);
    }

    /**
     * Add a credit card to the Vault.
     *
     * @param \CraigPaul\Moneris\CreditCard $card
     *
     * @return \CraigPaul\Moneris\Response
     */
    public function add(CreditCard $card)
    {
        $params = [
            'type' => 'res_add_cc',
            'crypt_type' => $card->crypt,
            'pan' => $card->number,
            'expdate' => $card->expiry,
        ];

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Delete a credit card from the Vault.
     *
     * @param string $key
     * @param \CraigPaul\Moneris\CreditCard $card
     *
     * @return \CraigPaul\Moneris\Response
     */
    public function delete(string $key)
    {
        $params = [
            'type' => 'res_delete',
            'data_key' => $key,
        ];

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Create a new Vault instance.
     *
     * @param string $id
     * @param string $token
     * @param string $environment
     *
     * @return $this
     */
    public static function create(string $id, string $token, string $environment)
    {
        return new static($id, $token, $environment);
    }

    /**
     * Get all expiring credit cards from the Moneris Vault.
     *
     * @return \CraigPaul\Moneris\Response
     */
    public function expiring()
    {
        $params = ['type' => 'res_get_expiring'];

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Peek into the Moneris Vault and retrieve a credit card
     * profile associated with a given data key.
     *
     * @param string $key
     * @param bool $full
     *
     * @return \CraigPaul\Moneris\Response
     */
    public function peek(string $key, bool $full = false)
    {
        $params = [
            'type' => $full ? 'res_lookup_full' : 'res_lookup_masked',
            'data_key' => $key,
        ];

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Tokenize a previous transaction to save the credit
     * card used in the Moneris Vault.
     *
     * @param $transaction
     * @param string|null $order
     *
     * @return \CraigPaul\Moneris\Response
     */
    public function tokenize($transaction, string $order = null)
    {
        if ($transaction instanceof Transaction) {
            $order = $transaction->order();
            $transaction = $transaction->number();
        }

        $params = [
            'type' => 'res_tokenize_cc',
            'txn_number' => $transaction,
            'order_id' => $order,
        ];

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }

    /**
     * Update an existing credit card in the Vault.
     *
     * @param string $key
     * @param \CraigPaul\Moneris\CreditCard $card
     *
     * @return \CraigPaul\Moneris\Response
     */
    public function update(string $key, CreditCard $card)
    {
        $params = [
            'type' => 'res_update_cc',
            'data_key' => $key,
            'crypt_type' => $card->crypt,
            'pan' => $card->number,
            'expdate' => $card->expiry,
        ];

        $transaction = $this->transaction($params);

        return $this->process($transaction);
    }
}