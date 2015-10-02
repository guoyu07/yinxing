## ID规划

Yinxing ID 使用 Bigint (13)，目标是将所有来源ID映射为一个唯一的YinxingID，且不同的来源有不同的区间

豆瓣ID目前使用Int(8)

if 豆瓣: Yinxing ID = 1,000,000,000,000 + 豆瓣ID

DMM ID 可能为：

- 英文+数字， 如：ipz00627, 1sdde00415
- 英文+下划线+数字：h_068mxgs00674
- 数字+英文+数字：118tus00025

可以对其使用CRC32 Hash，可以得到一个Int(10)。（注意：PHP的crc32在32bit系统下可能为负数）

if DMM: Yinxing ID = 2,000,000,000,000 + crc32(DMM ID)

