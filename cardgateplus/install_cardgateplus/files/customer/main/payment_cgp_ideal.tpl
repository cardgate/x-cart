{*
$Id: payment_cgp_ideal.tpl,v 1.4 2010/07/01 07:54:35 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
  {if $payment_cc_data.background eq 'I'}
    {$lng.disable_ccinfo_iframe_msg}
  {else}
  	{'true'|cgpbanks}
  {/if}
