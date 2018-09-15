<?php
/*
 * The MIT License
 *
 * Copyright 2018 Jordi Jolink.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * Check if the Criteo tags need to be inserted
 * @param $controller
 * @param $mode
 * @param $action
 * @param $dispatch_extra
 * @param $area
 */
function fn_soneritics_tradetracker_before_dispatch($controller, $mode, $action, $dispatch_extra, $area)
{
    if (AREA !== 'C') {
        return;
    }

    // Check if we are in the redirect part for TradeTracker
    $redirectDir = \Tygh\Registry::get('addons.soneritics_tradetracker.redirectdir');
    if (!empty($redirectDir)) {
        $requestDir = "/{$redirectDir}/";

        if (substr($_SERVER['REQUEST_URI'], 0, strlen($requestDir)) === $requestDir && isset($_GET['tt'])) {
            fn_soneritics_tradetracker_redirect();
        }
    }
}

/**
 * Redirect script as supplied by TradeTracker
 * @see https://sc.tradetracker.net/implementation/overview?f%5Blimit%5D=25&f%5Btarget%5D=merchant&f%5Bname%5D=General&f%5Bversion%5D=All&f%5BversionInfo%5D=Redirect&cid=28805&pid=43561
 */
function fn_soneritics_tradetracker_redirect()
{
    // Set domain name on which the redirect-page runs, WITHOUT "www.".
    $domainName = \Tygh\Registry::get('addons.soneritics_tradetracker.domain');

    // Set tracking group ID if provided by TradeTracker.
    $trackingGroupID = \Tygh\Registry::get('addons.soneritics_tradetracker.trackinggroupid');

    // Set the P3P compact policy.
    header('P3P: CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
    // Set parameters.
    $trackingParam = explode('_', $_GET['tt']);

    $campaignID = isset($trackingParam[0]) ? $trackingParam[0] : '';
    $materialID = isset($trackingParam[1]) ? $trackingParam[1] : '';
    $affiliateID = isset($trackingParam[2]) ? $trackingParam[2] : '';
    $reference = isset($trackingParam[3]) ? $trackingParam[3] : '';

    $redirectURL = isset($_GET['r']) ? $_GET['r'] : '';

    // Calculate MD5 checksum.
    $checkSum = md5('CHK_' . $campaignID . '::' . $materialID . '::' . $affiliateID . '::' . $reference);

    // Set tracking data.
    $trackingData = $materialID . '::' . $affiliateID . '::' . $reference . '::' . $checkSum . '::' . time();

    // Set regular tracking cookie.
    setcookie('TT2_' . $campaignID, $trackingData, time() + 31536000, '/', empty($domainName) ? null : '.' . $domainName);

    // Set session tracking cookie.
    setcookie('TTS_' . $campaignID, $trackingData, 0, '/', empty($domainName) ? null : '.' . $domainName);

    // Set tracking group cookie.
    if (!empty($trackingGroupID))
        setcookie('__tgdat' . $trackingGroupID, $trackingData . '_' . $campaignID, time() + 31536000, '/', empty($domainName) ? null : '.' . $domainName);

    // Set track-back URL.
    $trackBackURL = 'https://tc.tradetracker.net/?c=' . $campaignID . '&m=' . $materialID . '&a=' . $affiliateID . '&r=' . urlencode($reference) . '&u=' . urlencode($redirectURL);

    // Redirect to TradeTracker.
    header('Location: ' . $trackBackURL, true, 301);

    // Stop CsCart
    fn_flush();
    die;
}
