# FE-02 当前必要依赖（2026-09-27，性能决定已解决）

性能偏差为 ACCEPTED_DEVIATION_BY_USER，不再申请接受，不再部署基准环境、安装猜测数据库或安排冷热缓存/30次采样。原生性能任务及下方旧建议取消；既有功能PASS保留。

仅剩：确认solotochina.com技术身份的现成只读连接或管理员脱敏技术清单；实际目标引擎的必要正确性验证；实际SEO/设备验证边界；精确提交/推送、备份、部署、派生表/option、明确ID回填与启用权限。全部集中在[发布计划的唯一申请](acceptance/FE-02-production-plan.md)，不重复逐字段申请。当前无匹配目标连接或隔离栈证据，未新增环境。

---

## 以下为历史记录（当前决策以上文为准）

# FE-02 当前外部依赖（2026-09-27 发布准备）

旧v1.1地点正文缺口已由v1.2本地实现与证据解决，不是当前待开发事项。当前待办为：目标WP/PHP/数据库真实身份与获准隔离栈；原生DB并发/DDL/恢复及可能的持久缓存验证；目标HTTP性能决定；匹配SEO组合及真机检查；生产R/B/D/S/F/A独立权限。详见[当前报告](acceptance/FE-02-release-precheck.md)。当前停止，无后台任务。

---

# FE-01 外部依赖

本地开发与验收没有 CMS、付费模型或生产环境依赖。生产站点启用当前主题、真实文章封面质量、物理设备与线上 Core Web Vitals 仍需在各自授权流程中验证；本阶段未执行。

## FE-02 external checks

- A separately authorized production backfill is needed to index visible body text on already published posts. Local fixtures were backfilled only in disposable WordPress Playground databases.
- Check production P95, PHP/SQL time, and memory with representative content; the local Playground 300-post HTTP P95 exceeded 500 ms, including substantial baseline runtime overhead.
- Verify installed SEO plugin output, real Safari/iOS keyboard behavior, system text zoom, and accessibility tooling in their available environments.
- Define safe projection and invalidation for dynamic externally backed components if editorial search of that visible text is required.


## FE-02 v1.1 current correction

The earlier blanket dynamic-content wording is superseded by [the field inventory](acceptance/FE-02-projection-inventory.md). Place-card reader-visible core text remains a local functional gap; it is not excused as an optional commercial feature. Search latency remains FAIL_TARGET in the measured Playground environment. Production backfill, native PHP/DB comparison, actual SEO plugin versions, and physical-device checks are distinct remaining items. See [current acceptance](acceptance/FE-02-closeout.md) and [production plan](acceptance/FE-02-production-plan.md).
