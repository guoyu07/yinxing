# 银杏

[![License](https://poser.pugx.org/AlloVince/yinxing/license.svg)](https://packagist.org/packages/AlloVince/yinxing)
[![Build Status](https://travis-ci.org/AlloVince/yinxing.svg?branch=master)](https://travis-ci.org/AlloVince/yinxing)
[![Coverage Status](https://coveralls.io/repos/AlloVince/yinxing/badge.png?branch=master)](https://coveralls.io/r/AlloVince/yinxing?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/AlloVince/yinxing/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AlloVince/yinxing/?branch=master)

## ID规划

### Movie ID

Yinxing ID 使用 Bigint (13)，目标是将所有来源ID映射为一个唯一的YinxingID，且不同的来源有不同的区间

豆瓣ID目前使用Int(8)

if 豆瓣: Yinxing ID = 1,000,000,000,000 + 豆瓣ID

DMM ID 可能为：

- 英文+数字， 如：ipz00627, 1sdde00415
- 英文+下划线+数字：h_068mxgs00674
- 数字+英文+数字：118tus00025

可以对其使用CRC32 Hash，可以得到一个Int(10)。（注意：PHP的crc32在32bit系统下可能为负数, PHP 64bit下 最大int为9223372036854775807， 19位）

if DMM: Yinxing ID = 2,000,000,000,000 + crc32(DMM ID)

### 其他ID

其他ID暂时使用Int (10)

豆瓣影人ID max约为Int(7)

For 豆瓣： 1,000,000,000 + 豆瓣ID

DMM 其他ID （影人 + Maker）一般为Int（7）

For DMM：2,000,000,000 + DMM ID